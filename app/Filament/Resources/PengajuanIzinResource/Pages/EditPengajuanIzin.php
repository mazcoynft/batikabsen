<?php

namespace App\Filament\Resources\PengajuanIzinResource\Pages;

use App\Filament\Resources\PengajuanIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanIzin extends EditRecord
{
    protected static string $resource = PengajuanIzinResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
