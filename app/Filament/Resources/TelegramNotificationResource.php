<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelegramNotificationResource\Pages;
use App\Models\TelegramNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TelegramNotificationResource extends Resource
{
    protected static ?string $model = TelegramNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    
    protected static ?string $navigationLabel = 'Telegram Gateway';
    
    // Tambahkan atau ubah properti ini
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('bot_token')
                            ->label('Bot Token')
                            ->required()
                            ->helperText('Token dari BotFather Telegram'),
                            
                        Forms\Components\TextInput::make('bot_username')
                            ->label('Bot Username')
                            ->helperText('Username bot Telegram (contoh: batikabsen_bot)'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Notifikasi')
                            ->default(true),
                            
                        Forms\Components\Toggle::make('notify_attendance')
                            ->label('Notifikasi Absensi')
                            ->default(true),
                            
                        Forms\Components\Toggle::make('notify_leave_request')
                            ->label('Notifikasi Pengajuan Izin')
                            ->default(true),
                    ])->columnSpan(1),
                    
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Textarea::make('welcome_message')
                            ->label('Pesan Selamat Datang')
                            ->default("Selamat datang di BATIK App Bot! 👋\n\nSilahkan registrasi untuk mendapatkan notifikasi dari BatikApps dengan cara:\n\n/register karyawan@email.com")
                            ->rows(4),
                            
                        Forms\Components\Textarea::make('register_success_message')
                            ->label('Pesan Sukses Registrasi Karyawan')
                            ->default("✅ Berhasil mendaftarkan Telegram untuk karyawan: {nama_karyawan}\n\nAnda akan menerima notifikasi absensi dan pengajuan izin melalui bot ini.")
                            ->helperText('Gunakan {nama_karyawan} sebagai placeholder untuk nama karyawan')
                            ->rows(4),
                            
                        Forms\Components\Textarea::make('admin_register_success_message')
                            ->label('Pesan Sukses Registrasi Admin')
                            ->default("✅ Berhasil mendaftarkan Telegram untuk ADMIN: {nama_admin}")
                            ->helperText('Gunakan {nama_admin} sebagai placeholder untuk nama admin')
                            ->rows(4),
                    ])->columnSpan(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bot_username')
                    ->label('Bot Username'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
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
            'index' => Pages\ListTelegramNotifications::route('/'),
            'create' => Pages\CreateTelegramNotification::route('/create'),
            'edit' => Pages\EditTelegramNotification::route('/{record}/edit'),
        ];
    }
}