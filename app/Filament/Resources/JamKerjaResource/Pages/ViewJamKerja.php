<?php

namespace App\Filament\Resources\JamKerjaResource\Pages;

use App\Filament\Resources\JamKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJamKerja extends ViewRecord
{
    protected static string $resource = JamKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}