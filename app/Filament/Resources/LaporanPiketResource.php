<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanPiketResource\Pages;
use App\Models\PengajuanPiket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LaporanPiketResource extends Resource
{
    protected static ?string $model = PengajuanPiket::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static ?string $navigationLabel = 'Laporan Piket';
    
    protected static ?string $navigationGroup = 'Laporan';
    
    protected static ?int $navigationSort = 1;

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
                Tables\Columns\TextColumn::make('nama_karyawan')
                    ->label('Nama Karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_awal_piket')
                    ->label('Tanggal Awal')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('tanggal_akhir_piket')
                    ->label('Tanggal Akhir')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('jenis_piket')
                    ->label('Jenis Piket')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hari Biasa' => 'success',
                        'Hari Libur' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('nama_lembaga')
                    ->label('Lembaga')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_hari')
                    ->label('Jumlah Hari')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('nominal_piket')
                    ->label('Nominal')
                    ->money('IDR'),
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
            'index' => Pages\ListLaporanPikets::route('/'),
            'generate' => Pages\GenerateLaporanPiket::route('/generate'),
            'view' => Pages\ViewLaporanPiket::route('/{record}'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
