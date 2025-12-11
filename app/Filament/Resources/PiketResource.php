<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PiketResource\Pages;
use App\Models\Piket;
use App\Models\Karyawan;
use App\Models\TelegramNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class PiketResource extends Resource
{
    protected static ?string $model = Piket::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Jadwal Piket';
    
    protected static ?string $modelLabel = 'Piket';
    
    protected static ?string $pluralModelLabel = 'Piket';
    
    protected static ?string $navigationGroup = 'Presensi';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nik')
                    ->label('Karyawan')
                    ->options(Karyawan::all()->pluck('nama', 'nik'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $karyawan = Karyawan::where('nik', $state)->first();
                        if ($karyawan) {
                            $set('nama_karyawan', $karyawan->nama);
                        }
                    }),
                    
                Forms\Components\TextInput::make('nama_karyawan')
                    ->label('Nama Karyawan')
                    ->disabled()
                    ->dehydrated(),
                    
                Forms\Components\DatePicker::make('tanggal_awal_piket')
                    ->label('Tanggal Awal Piket')
                    ->required(),
                    
                Forms\Components\DatePicker::make('tanggal_akhir_piket')
                    ->label('Tanggal Akhir Piket')
                    ->required(),
                    
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->required()
                    ->placeholder('Contoh: Monitoring client dan support client'),
                    
                Forms\Components\TextInput::make('jumlah_hari')
                    ->label('Jumlah Hari')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jumlahHari = (int) $state;
                        $jumlahHariLibur = (int) $get('jumlah_hari_libur') ?? 0;
                        $jenispiket = $get('jenis_piket');
                        
                        if ($jenispiket === 'hari_libur') {
                            $nominal = $jumlahHariLibur * 50000;
                        } else {
                            $nominal = ($jumlahHari * 25000) + ($jumlahHariLibur * 50000);
                        }
                        
                        $set('nominal_piket', $nominal);
                    }),
                    
                Forms\Components\TextInput::make('jumlah_hari_libur')
                    ->label('Jumlah Hari Libur')
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jumlahHari = (int) $get('jumlah_hari') ?? 0;
                        $jumlahHariLibur = (int) $state;
                        $jenispiket = $get('jenis_piket');
                        
                        if ($jenispiket === 'hari_libur') {
                            $nominal = $jumlahHariLibur * 50000;
                        } else {
                            $nominal = ($jumlahHari * 25000) + ($jumlahHariLibur * 50000);
                        }
                        
                        $set('nominal_piket', $nominal);
                    }),
                    
                Forms\Components\TextInput::make('nominal_piket')
                    ->label('Nominal Piket')
                    ->prefix('Rp')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                    
                Forms\Components\Select::make('jenis_piket')
                    ->label('Jenis Piket')
                    ->options([
                        'piket_mingguan' => 'Piket Mingguan',
                        'piket_khusus' => 'Piket Khusus',
                        'hari_libur' => 'Hari Libur'
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jumlahHari = (int) $get('jumlah_hari') ?? 0;
                        $jumlahHariLibur = (int) $get('jumlah_hari_libur') ?? 0;
                        
                        if ($state === 'hari_libur') {
                            $nominal = $jumlahHariLibur * 50000;
                        } else {
                            $nominal = ($jumlahHari * 25000) + ($jumlahHariLibur * 50000);
                        }
                        
                        $set('nominal_piket', $nominal);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('nama_karyawan')
                    ->label('Nama Karyawan')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('tanggal_awal_piket')
                    ->label('Tanggal Awal')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('tanggal_akhir_piket')
                    ->label('Tanggal Akhir')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('jenis_piket')
                    ->label('Jenis Piket')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'piket_mingguan' => 'success',
                        'piket_khusus' => 'warning',
                        'hari_libur' => 'danger',
                    }),
                    
                Tables\Columns\TextColumn::make('nominal_piket')
                    ->label('Nominal')
                    ->money('IDR'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_piket')
                    ->options([
                        'piket_mingguan' => 'Piket Mingguan',
                        'piket_khusus' => 'Piket Khusus',
                        'hari_libur' => 'Hari Libur'
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected'
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Piket $record) {
                        $record->update(['status' => 'approved']);
                        
                        // Get karyawan and user
                        $karyawan = Karyawan::where('nik', $record->nik)->first();
                        $user = $karyawan ? $karyawan->user : null;
                        
                        // Send notification to employee
                        if ($user && $user->id_chat_telegram) {
                            self::sendTelegramNotification(
                                $user->id_chat_telegram,
                                "✅ *Piket Disetujui*\n\n" .
                                "Nama: {$record->nama_karyawan}\n" .
                                "Tanggal: " . $record->tanggal_awal_piket->format('d/m/Y') . " - " . $record->tanggal_akhir_piket->format('d/m/Y') . "\n" .
                                "Jenis: " . ucwords(str_replace('_', ' ', $record->jenis_piket)) . "\n" .
                                "Nominal: Rp " . number_format($record->nominal_piket, 0, ',', '.') . "\n\n" .
                                "Pengajuan piket Anda telah disetujui oleh admin."
                            );
                        }
                        
                        Notification::make()
                            ->success()
                            ->title('Piket Approved')
                            ->body('Piket berhasil disetujui dan notifikasi telah dikirim.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Piket $record) => $record->status === 'pending'),
                    
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(function (Piket $record) {
                        $record->update(['status' => 'rejected']);
                        
                        // Get karyawan and user
                        $karyawan = Karyawan::where('nik', $record->nik)->first();
                        $user = $karyawan ? $karyawan->user : null;
                        
                        // Send notification to employee
                        if ($user && $user->id_chat_telegram) {
                            self::sendTelegramNotification(
                                $user->id_chat_telegram,
                                "❌ *Piket Ditolak*\n\n" .
                                "Nama: {$record->nama_karyawan}\n" .
                                "Tanggal: " . $record->tanggal_awal_piket->format('d/m/Y') . " - " . $record->tanggal_akhir_piket->format('d/m/Y') . "\n" .
                                "Jenis: " . ucwords(str_replace('_', ' ', $record->jenis_piket)) . "\n\n" .
                                "Pengajuan piket Anda ditolak oleh admin."
                            );
                        }
                        
                        Notification::make()
                            ->danger()
                            ->title('Piket Rejected')
                            ->body('Piket berhasil ditolak dan notifikasi telah dikirim.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Piket $record) => $record->status === 'pending'),
                    
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->action(function (Piket $record) {
                        $record->update(['status' => 'pending']);
                        
                        // Get karyawan and user
                        $karyawan = Karyawan::where('nik', $record->nik)->first();
                        $user = $karyawan ? $karyawan->user : null;
                        
                        // Send notification to employee
                        if ($user && $user->id_chat_telegram) {
                            self::sendTelegramNotification(
                                $user->id_chat_telegram,
                                "⚠️ *Piket Dibatalkan*\n\n" .
                                "Nama: {$record->nama_karyawan}\n" .
                                "Tanggal: " . $record->tanggal_awal_piket->format('d/m/Y') . " - " . $record->tanggal_akhir_piket->format('d/m/Y') . "\n\n" .
                                "Status piket Anda telah dibatalkan oleh admin dan dikembalikan ke status pending."
                            );
                        }
                        
                        Notification::make()
                            ->warning()
                            ->title('Piket Cancelled')
                            ->body('Status piket dikembalikan ke pending dan notifikasi telah dikirim.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Piket $record) => in_array($record->status, ['approved', 'rejected'])),
                    
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPikets::route('/'),
            'create' => Pages\CreatePiket::route('/create'),
            'edit' => Pages\EditPiket::route('/{record}/edit'),
        ];
    }
    
    // Helper method for sending Telegram notifications
    private static function sendTelegramNotification($chatId, $message)
    {
        if (!$chatId) {
            Log::warning('Telegram notification skipped: No chat ID provided');
            return;
        }
        
        try {
            $telegramSettings = TelegramNotification::first();
            
            if (!$telegramSettings || !$telegramSettings->is_active || !$telegramSettings->bot_token) {
                Log::warning('Telegram notification skipped: Telegram not configured');
                return;
            }
            
            $response = Http::post("https://api.telegram.org/bot{$telegramSettings->bot_token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            if ($response->successful()) {
                Log::info('Telegram notification sent successfully to chat ID: ' . $chatId);
            } else {
                Log::error('Telegram notification failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Telegram notification exception: ' . $e->getMessage());
        }
    }
    
    // Disable create button - only accept from frontend
    public static function canCreate(): bool
    {
        return false;
    }
}