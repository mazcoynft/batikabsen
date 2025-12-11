<?php

namespace App\Filament\Resources\JamKerjaResource\Pages;

use App\Filament\Resources\JamKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJamKerjas extends ListRecords
{
    protected static string $resource = JamKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
