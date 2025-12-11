<?php

namespace App\Filament\Resources\DokumenKaryawanResource\Pages;

use App\Filament\Resources\DokumenKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDokumenKaryawans extends ListRecords
{
    protected static string $resource = DokumenKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
