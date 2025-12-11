<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokumenKaryawanResource\Pages;
use App\Models\DokumenKaryawan;
use App\Models\Karyawan;
use App\Models\TelegramNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class DokumenKaryawanResource extends Resource
{
    protected static ?string $model = DokumenKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Slip Gaji & Dokumen';
    
    protected static ?string $modelLabel = 'Dokumen';
    
    protected static ?string $pluralModelLabel = 'Slip Gaji & Dokumen';
    
    protected static ?string $navigationGroup = 'Data Master';
    
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipe')
                    ->label('Tipe Dokumen')
                    ->options([
                        'slip_gaji' => 'Slip Gaji',
                        'dokumen' => 'Dokumen'
                    ])
                    ->required()
                    ->reactive(),
                    
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')
                    ->required()
                    ->placeholder('Contoh: Slip Gaji Desember 2025'),
                    
                Forms\Components\FileUpload::make('file_path')
                    ->label('Upload PDF')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('dokumen-karyawan')
                    ->required()
                    ->maxSize(5120), // 5MB
                    
                Forms\Components\Checkbox::make('kirim_semua')
                    ->label('Kirim ke Semua Karyawan')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('karyawan_ids', []);
                        }
                    })
                    ->helperText('Centang ini jika ingin mengirim ke semua karyawan')
                    ->columnSpanFull()
                    ->extraAttributes([
                        'style' => 'background: #fef3c7; padding: 1rem; border-radius: 8px; border: 2px solid #f59e0b;'
                    ]),
                    
                Forms\Components\Select::make('karyawan_ids')
                    ->label('Pilih Karyawan')
                    ->multiple()
                    ->options(Karyawan::all()->pluck('nama', 'nik'))
                    ->searchable()
                    ->placeholder('Pilih karyawan yang akan menerima dokumen')
                    ->helperText('Pilih satu atau lebih karyawan. Abaikan jika sudah centang "Kirim ke Semua"')
                    ->hidden(fn (callable $get) => $get('kirim_semua') === true),
                    
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Keterangan tambahan (opsional)')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'slip_gaji' => 'success',
                        'dokumen' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'slip_gaji' => 'Slip Gaji',
                        'dokumen' => 'Dokumen',
                    }),
                    
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('nama_karyawan')
                    ->label('Karyawan')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->options([
                        'slip_gaji' => 'Slip Gaji',
                        'dokumen' => 'Dokumen'
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (DokumenKaryawan $record): string => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokumenKaryawans::route('/'),
            'create' => Pages\CreateDokumenKaryawan::route('/create'),
        ];
    }
}
