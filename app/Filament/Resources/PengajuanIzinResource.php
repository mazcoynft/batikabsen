<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanIzinResource\Pages;
use App\Models\PengajuanIzin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Services\TelegramBotService;
use App\Models\Cuti;
use App\Models\Presensi;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;

class PengajuanIzinResource extends Resource
{
    protected static ?string $model = PengajuanIzin::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pengajuan Izin & Cuti';

    // Tambahkan atau ubah properti ini
    protected static ?string $navigationGroup = 'Presensi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form schema can be defined here if needed for create/edit views.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_izin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('karyawan.nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->label('Nama Karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_awal')
                    ->label('Tanggal')
                    ->formatStateUsing(function (PengajuanIzin $record): string {
                        $start = Carbon::parse($record->tanggal_awal);
                        $end = Carbon::parse($record->tanggal_akhir);

                        if ($start->isSameDay($end)) {
                            return $start->translatedFormat('d F Y');
                        }

                        if ($start->isSameMonth($end) && $start->isSameYear($end)) {
                            return $start->format('d') . '-' . $end->format('d') . ' ' . $start->translatedFormat('F Y');
                        }
                        
                        return $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
                    }),
                Tables\Columns\TextColumn::make('jenis_pengajuan')
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('jumlah_hari')
                    ->label('Jumlah Hari')
                    ->formatStateUsing(fn (string $state): string => "{$state} hari"),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                Filter::make('nik')
                    ->form([
                        TextInput::make('nik')->label('NIK Karyawan'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nik'],
                            fn (Builder $query, $nik): Builder => $query->whereHas('karyawan', fn (Builder $query) => $query->where('nik', 'like', "%{$nik}%"))
                        );
                    }),
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_awal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_akhir', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['dari_tanggal'] || ! $data['sampai_tanggal']) {
                            return null;
                        }
                 
                        return 'Tanggal ' . Carbon::parse($data['dari_tanggal'])->toFormattedDateString() . ' - ' . Carbon::parse($data['sampai_tanggal'])->toFormattedDateString();
                    }),
                SelectFilter::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->options([
                        'CUTI' => 'Cuti',
                        'IZIN' => 'Izin',
                        'SAKIT' => 'Sakit',
                    ])
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (PengajuanIzin $record, TelegramBotService $telegramService) {
                        $isAnnualLeave = $record->cuti && $record->cuti->nama_cuti === 'Cuti Tahunan';
                        $sisaCutiBaru = $record->sisa_cuti;

                        if ($isAnnualLeave) {
                            $lastApprovedLeave = PengajuanIzin::where('karyawan_id', $record->karyawan_id)
                                ->where('status', 'approved')
                                ->whereHas('cuti', fn($q) => $q->where('nama_cuti', 'Cuti Tahunan'))
                                ->latest('approved_at')
                                ->first();

                            $previousLeaveBalance = $lastApprovedLeave ? $lastApprovedLeave->sisa_cuti : 12;
                            $sisaCutiBaru = $previousLeaveBalance - $record->jumlah_hari;
                        }

                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'sisa_cuti' => $sisaCutiBaru,
                            'sisa_cuti_default' => 12, // Set default value
                        ]);

                        // Refresh data for notification
                        $record->refresh();

                        // Buat record presensi untuk setiap hari cuti/izin/sakit
                        $startDate = Carbon::parse($record->tanggal_awal);
                        $endDate = Carbon::parse($record->tanggal_akhir);
                        $currentDate = clone $startDate;

                        // Tentukan status berdasarkan jenis pengajuan
                        $status = match(strtoupper($record->jenis_pengajuan)) {
                            'CUTI' => 'c',
                            'SAKIT' => 's',
                            'IZIN' => 'i',
                            default => null
                        };

                        // Jika status valid, buat record presensi untuk setiap hari
                        if ($status) {
                            while ($currentDate->lte($endDate)) {
                                // Cek apakah sudah ada record presensi untuk tanggal ini
                                $existingRecord = Presensi::where('karyawan_id', $record->karyawan_id)
                                    ->whereDate('tgl_presensi', $currentDate->format('Y-m-d'))
                                    ->first();
                                
                                // Jika belum ada record, buat baru
                                if (!$existingRecord) {
                                    Presensi::create([
                                        'karyawan_id' => $record->karyawan_id,
                                        'tgl_presensi' => $currentDate->format('Y-m-d'),
                                        'status' => $status,
                                        'kode_izin' => $record->kode_izin
                                    ]);
                                }
                                
                                $currentDate->addDay();
                            }
                        }

                        $telegramService->sendLeaveApprovalNotification($record);
                    })
                    ->visible(fn (PengajuanIzin $record) => $record->status === 'pending'),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(function (PengajuanIzin $record, TelegramBotService $telegramService) {
                        $record->update(['status' => 'rejected', 'rejected_by' => auth()->id(), 'rejected_at' => now()]);
                        $telegramService->sendLeaveRejectionNotification($record);
                    })
                    ->visible(fn (PengajuanIzin $record) => $record->status === 'pending'),
                Action::make('cancel')
                    ->label('Batalkan')
                    ->color('gray')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->action(function (PengajuanIzin $record, TelegramBotService $telegramService) {
                        $record->update([
                            'status' => 'pending', 
                            'approved_by' => null, 'approved_at' => null,
                            'rejected_by' => null, 'rejected_at' => null,
                        ]);
                        $telegramService->sendLeaveCancellationNotification($record);
                    })
                    ->visible(fn (PengajuanIzin $record) => in_array($record->status, ['approved', 'rejected'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanIzins::route('/'),
            'create' => Pages\CreatePengajuanIzin::route('/create'),
            
        ];
    }
}