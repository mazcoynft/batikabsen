<?php

namespace App\Filament\Resources\PengajuanIzinResource\Pages;

use App\Filament\Resources\PengajuanIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuanIzin extends CreateRecord
{
    protected static string $resource = PengajuanIzinResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
