<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LemburResource\Pages;
use App\Models\Lembur;
use App\Models\Karyawan;
use App\Models\TelegramNotification;
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

class LemburResource extends Resource
{
    protected static ?string $model = Lembur::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'Lembur';
    
    protected static ?string $modelLabel = 'Lembur';
    
    protected static ?string $pluralModelLabel = 'Lembur';
    
    protected static ?string $navigationGroup = 'Presensi';
    
    protected static ?int $navigationSort = 4;

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
                            $set('nama', $karyawan->nama);
                        }
                    }),
                    
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Karyawan')
                    ->disabled()
                    ->dehydrated(),
                    
                Forms\Components\DatePicker::make('tanggal_awal_lembur')
                    ->label('Tanggal Awal Lembur')
                    ->required(),
                    
                Forms\Components\DatePicker::make('tanggal_akhir_lembur')
                    ->label('Tanggal Akhir Lembur')
                    ->required(),
                    
                Forms\Components\TextInput::make('nama_lembaga')
                    ->label('Nama Lembaga')
                    ->required()
                    ->placeholder('Contoh: BMT Bahtera'),
                    
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->required()
                    ->placeholder('Contoh: Update database, maintenance server'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Karyawan')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('tanggal_awal_lembur')
                    ->label('Tanggal Awal')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('tanggal_akhir_lembur')
                    ->label('Tanggal Akhir')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('nama_lembaga')
                    ->label('Nama Lembaga')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50),
                    
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
                    ->action(function (Lembur $record) {
                        $record->update(['status' => 'approved']);
                        
                        // Get karyawan and user
                        $karyawan = Karyawan::where('nik', $record->nik)->first();
                        $user = $karyawan ? $karyawan->user : null;
                        
                        // Send notification to employee
                        if ($user && $user->id_chat_telegram) {
                            self::sendTelegramNotification(
                                $user->id_chat_telegram,
                                "✅ *Lembur Disetujui*\n\n" .
                                "Nama: {$record->nama}\n" .
                                "Tanggal: " . $record->tanggal_awal_lembur->format('d/m/Y') . " - " . $record->tanggal_akhir_lembur->format('d/m/Y') . "\n" .
                                "Lembaga: {$record->nama_lembaga}\n\n" .
                                "Pengajuan lembur Anda telah disetujui oleh admin."
                            );
                        }
                        
                        Notification::make()
                            ->success()
                            ->title('Lembur Approved')
                            ->body('Lembur berhasil disetujui dan notifikasi telah dikirim.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Lembur $record) => $record->status === 'pending'),
                    
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(function (Lembur $record) {
                        $record->update(['status' => 'rejected']);
                        
                        // Get karyawan and user
                        $karyawan = Karyawan::where('nik', $record->nik)->first();
                        $user = $karyawan ? $karyawan->user : null;
                        
                        // Send notification to employee
                        if ($user && $user->id_chat_telegram) {
                            self::sendTelegramNotification(
                                $user->id_chat_telegram,
                                "❌ *Lembur Ditolak*\n\n" .
                                "Nama: {$record->nama}\n" .
                                "Tanggal: " . $record->tanggal_awal_lembur->format('d/m/Y') . " - " . $record->tanggal_akhir_lembur->format('d/m/Y') . "\n" .
                                "Lembaga: {$record->nama_lembaga}\n\n" .
                                "Pengajuan lembur Anda ditolak oleh admin."
                            );
                        }
                        
                        Notification::make()
                            ->danger()
                            ->title('Lembur Rejected')
                            ->body('Lembur berhasil ditolak dan notifikasi telah dikirim.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Lembur $record) => $record->status === 'pending'),
                    
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->action(function (Lembur $record) {
                        $record->update(['status' => 'pending']);
                        
                        // Get karyawan and user
                        $karyawan = Karyawan::where('nik', $record->nik)->first();
                        $user = $karyawan ? $karyawan->user : null;
                        
                        // Send notification to employee
                        if ($user && $user->id_chat_telegram) {
                            self::sendTelegramNotification(
                                $user->id_chat_telegram,
                                "⚠️ *Lembur Dibatalkan*\n\n" .
                                "Nama: {$record->nama}\n" .
                                "Tanggal: " . $record->tanggal_awal_lembur->format('d/m/Y') . " - " . $record->tanggal_akhir_lembur->format('d/m/Y') . "\n" .
                                "Lembaga: {$record->nama_lembaga}\n\n" .
                                "Status lembur Anda telah dibatalkan oleh admin dan dikembalikan ke status pending."
                            );
                        }
                        
                        Notification::make()
                            ->warning()
                            ->title('Lembur Cancelled')
                            ->body('Status lembur dikembalikan ke pending dan notifikasi telah dikirim.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Lembur $record) => in_array($record->status, ['approved', 'rejected'])),
                    
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // ✅ DIPERBAIKI: Hapus array kosong
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLemburs::route('/'),
            // 'create' => Pages\CreateLembur::route('/create'), // Disabled - only accept from frontend
            'view' => Pages\ViewLembur::route('/{record}'),
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
            
            if (!$telegramSettings) {
                Log::warning('Telegram notification skipped: No telegram settings found');
                return;
            }
            
            if (!$telegramSettings->is_active) {
                Log::warning('Telegram notification skipped: Telegram is not active');
                return;
            }
            
            if (!$telegramSettings->bot_token) {
                Log::warning('Telegram notification skipped: No bot token configured');
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
    
    // Disable create button
    public static function canCreate(): bool
    {
        return false;
    }
}