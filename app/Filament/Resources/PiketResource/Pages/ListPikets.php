<?php

namespace App\Filament\Resources\PiketResource\Pages;

use App\Filament\Resources\PiketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPikets extends ListRecords
{
    protected static string $resource = PiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}