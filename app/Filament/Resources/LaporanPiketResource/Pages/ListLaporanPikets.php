<?php

namespace App\Filament\Resources\LaporanPiketResource\Pages;

use App\Filament\Resources\LaporanPiketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanPikets extends ListRecords
{
    protected static string $resource = LaporanPiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_report')
                ->label('Generate Laporan')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->url(route('filament.admin.resources.laporan-pikets.generate')),
        ];
    }
}
