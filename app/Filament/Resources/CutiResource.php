<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CutiResource\Pages;
use App\Models\Cuti;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CutiResource extends Resource
{
    protected static ?string $model = Cuti::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    // Tambahkan atau ubah properti ini
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 4;

    // Tambahkan properti berikut di class CutiResource
    protected static ?string $navigationLabel = 'Cuti';
    protected static ?string $modelLabel = 'Cuti';
    protected static ?string $pluralModelLabel = 'Cuti';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_cuti')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('nama_cuti')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jumlah_hari')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('hari'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_cuti')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_cuti')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_hari')
                    ->numeric()
                    ->suffix(' hari'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCutis::route('/'),
            'create' => Pages\CreateCuti::route('/create'),
            'edit' => Pages\EditCuti::route('/{record}/edit'),
        ];
    }
}