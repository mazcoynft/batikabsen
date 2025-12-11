<?php

namespace App\Filament\Resources\PengajuanPiketResource\Pages;

use App\Filament\Resources\PengajuanPiketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanPikets extends ListRecords
{
    protected static string $resource = PengajuanPiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
