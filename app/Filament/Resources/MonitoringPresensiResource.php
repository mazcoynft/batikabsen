<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringPresensiResource\Pages;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\JamKerja;
use App\Models\JamKerjaKaryawan;
use App\Models\PengajuanIzin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ViewColumn;

class MonitoringPresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Monitoring Presensi';
    
    // Tambahkan atau ubah properti ini
    protected static ?string $navigationGroup = 'Presensi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form fields jika diperlukan untuk edit/create
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                    
                Tables\Columns\TextColumn::make('karyawan.nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('jam_in')
                    ->label('Jam Masuk')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i:s') : '-')
                    ->color(function ($record) {
                        if (!$record->jam_in) return 'gray';
                        
                        $jamKerja = $record->jamKerja;
                        if ($jamKerja && Carbon::parse($record->jam_in)->gt(Carbon::parse($jamKerja->jam_masuk))) {
                            return 'danger'; // Terlambat
                        }
                        return 'success'; // Tepat waktu
                    }),
                    
                Tables\Columns\ImageColumn::make('foto_in')
                    ->label('Foto Masuk')
                    ->square()
                    ->size(40)
                    ->defaultImageUrl('/images/no-image.png'),
                    
                Tables\Columns\TextColumn::make('jam_out')
                    ->label('Jam Pulang')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i:s') : 'Belum ada'),
                    
                Tables\Columns\TextColumn::make('foto_out_display')
                    ->label('Foto Pulang')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->foto_out) {
                            return 'Ada';
                        }
                        return 'Belum ada';
                    })
                    ->color(function ($record) {
                        return $record->foto_out ? 'success' : 'gray';
                    }),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function (string $state, $record) {
                        switch ($state) {
                            case 'h':
                                // Cek apakah terlambat
                                $jamKerja = $record->jamKerja;
                                if ($jamKerja && $record->jam_in && Carbon::parse($record->jam_in)->gt(Carbon::parse($jamKerja->jam_masuk))) {
                                    return 'Terlambat';
                                }
                                return 'Hadir Tepat Waktu';
                            case 's':
                                return 'Sakit';
                            case 'i':
                                return 'Izin';
                            case 'c':
                                return 'Cuti';
                            default:
                                return 'Alpha';
                        }
                    })
                    ->color(fn ($state, $record): string => match ($state) {
                        'h' => (function() use ($record) {
                            $jamKerja = $record->jamKerja;
                            if ($jamKerja && $record->jam_in && Carbon::parse($record->jam_in)->gt(Carbon::parse($jamKerja->jam_masuk))) {
                                return 'danger'; // Terlambat = merah
                            }
                            return 'success'; // Tepat waktu = hijau
                        })(),
                        's' => 'warning',  // Sakit = kuning
                        'i' => 'info',     // Izin = biru
                        'c' => 'primary',  // Cuti = ungu
                        default => 'gray', // Alpha = abu
                    }),
                    

                    
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->formatStateUsing(function ($record) {
                        // Jika status izin/sakit/cuti, ambil dari pengajuan_izin
                        if (in_array($record->status, ['s', 'i', 'c'])) {
                            $pengajuan = PengajuanIzin::where('karyawan_id', $record->karyawan_id)
                                ->whereDate('tanggal_awal', '<=', $record->tgl_presensi)
                                ->whereDate('tanggal_akhir', '>=', $record->tgl_presensi)
                                ->where('status', 'approved')
                                ->first();
                            return $pengajuan ? $pengajuan->keterangan : 'Tidak ada keterangan';
                        }
                        // Jika status hadir, ambil dari field keterangan presensi
                        return $record->keterangan ?: '-';
                    })
                    ->limit(50),
            ])
            ->defaultSort('tgl_presensi', 'desc')
            ->filters([
                Tables\Filters\Filter::make('tgl_presensi')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Filter Tanggal')
                            ->placeholder('Pilih tanggal untuk filter')
                            ->default(now()->format('Y-m-d')), // Default ke hari ini
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_presensi', $date),
                                // Jika tidak ada tanggal yang dipilih, default ke hari ini
                                fn (Builder $query): Builder => $query->whereDate('tgl_presensi', now()->format('Y-m-d'))
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        $tanggal = $data['tanggal'] ?? now()->format('Y-m-d');
                        $indicators['tanggal'] = 'Tanggal: ' . Carbon::parse($tanggal)->format('d/m/Y');
                        return $indicators;
                    })
                    ->default(), // Aktifkan filter secara default
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'h' => 'Hadir',
                        's' => 'Sakit',
                        'i' => 'Izin',
                        'c' => 'Cuti',
                    ])
                    ->placeholder('Semua Status'),
                    
                Tables\Filters\SelectFilter::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama')
                    ->searchable()
                    ->placeholder('Semua Karyawan'),
            ])
            ->actions([
                // Hapus tombol view
            ])
            ->bulkActions([
                // Bulk actions jika diperlukan
            ])
            ->defaultPaginationPageOption(25)
            ->poll('30s'); // Auto refresh setiap 30 detik
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
            'index' => Pages\ListMonitoringPresensis::route('/'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        // Load relasi yang diperlukan dan filter default ke hari ini
        return parent::getEloquentQuery()
            ->with([
                'karyawan', 
                'jamKerja'
            ])
            ->whereDate('tgl_presensi', now()->format('Y-m-d')) // Default filter hari ini
            ->orderBy('tgl_presensi', 'desc')
            ->orderBy('jam_in', 'asc');
    }
}
