<?php

namespace App\Filament\Resources\PiketResource\Pages;

use App\Filament\Resources\PiketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPiket extends EditRecord
{
    protected static string $resource = PiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}