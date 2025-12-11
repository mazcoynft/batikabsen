<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanLemburResource\Pages;
use App\Models\Lembur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LaporanLemburResource extends Resource
{
    protected static ?string $model = Lembur::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Laporan Lembur';
    
    protected static ?string $navigationGroup = 'Laporan';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('tanggal_akhir_lembur')
                    ->label('Tanggal Akhir')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('nama_lembaga')
                    ->label('Lembaga')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListLaporanLemburs::route('/'),
            'generate' => Pages\GenerateLaporanLembur::route('/generate'),
            'view' => Pages\ViewLaporanLembur::route('/{record}'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
