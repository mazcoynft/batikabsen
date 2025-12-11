<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JamKerjaResource\Pages;
use App\Models\JamKerja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JamKerjaResource extends Resource
{
    protected static ?string $model = JamKerja::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    // Tambahkan atau ubah properti ini
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Jam Kerja';
    protected static ?string $modelLabel = 'Jam Kerja';
    protected static ?string $pluralModelLabel = 'Jam Kerja';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jam Kerja')
                    ->schema([
                        Forms\Components\TextInput::make('kode_jam_kerja')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nama_jam_kerja')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TimePicker::make('awal_jam_masuk')
                            ->required()
                            ->seconds(true)
                            ->displayFormat('H:i:s')
                            ->label('Awal Jam Masuk')
                            ->helperText('Waktu karyawan bisa mulai melakukan absen di pagi hari'),
                        Forms\Components\TimePicker::make('jam_masuk')
                            ->required()
                            ->seconds(true)
                            ->displayFormat('H:i:s')
                            ->label('Jam Masuk')
                            ->helperText('Batas karyawan melakukan absen di pagi hari, jika melebihi jam ini maka karyawan terlambat'),
                        Forms\Components\TimePicker::make('akhir_jam_masuk')
                            ->required()
                            ->seconds(true)
                            ->displayFormat('H:i:s')
                            ->label('Akhir Jam Masuk')
                            ->helperText('Batas terlambat karyawan bisa masuk, jika melebihi jam ini maka karyawan dianggap alpha'),
                        Forms\Components\TimePicker::make('jam_pulang')
                            ->required()
                            ->seconds(true)
                            ->displayFormat('H:i:s')
                            ->label('Jam Pulang')
                            ->helperText('Jam dimana karyawan bisa melakukan absen pulang'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_jam_kerja')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_jam_kerja')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('awal_jam_masuk')
                    ->time('H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_masuk')
                    ->time('H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('akhir_jam_masuk')
                    ->time('H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_pulang')
                    ->time('H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListJamKerjas::route('/'),
            'create' => Pages\CreateJamKerja::route('/create'),
            'view' => Pages\ViewJamKerja::route('/{record}'),
            'edit' => Pages\EditJamKerja::route('/{record}/edit'),
        ];
    }
}
