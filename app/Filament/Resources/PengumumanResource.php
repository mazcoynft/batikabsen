<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumumanResource\Pages;
use App\Models\Pengumuman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Pengumuman';
    protected static ?string $modelLabel = 'Pengumuman';
    protected static ?string $pluralModelLabel = 'Pengumuman';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengumuman')
                    ->schema([
                        Forms\Components\TextInput::make('no_urut')
                            ->label('No Urut')
                            ->numeric()
                            ->default(1),
                            
                        Forms\Components\TextInput::make('jenis_pengumuman')
                            ->label('Jenis Pengumuman')
                            ->required()
                            ->maxLength(100),
                            
                        Forms\Components\Textarea::make('isi_pengumuman')
                            ->label('Isi Pengumuman')
                            ->required()
                            ->rows(5),
                            
                        Forms\Components\DateTimePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required(),
                            
                        Forms\Components\DateTimePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required(),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_urut')
                    ->label('No Urut')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('jenis_pengumuman')
                    ->label('Jenis Pengumuman')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('isi_pengumuman')
                    ->label('Isi Pengumuman')
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
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
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengumuman::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }
}