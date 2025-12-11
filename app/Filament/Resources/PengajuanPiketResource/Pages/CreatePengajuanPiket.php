<?php

namespace App\Filament\Resources\PengajuanPiketResource\Pages;

use App\Filament\Resources\PengajuanPiketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuanPiket extends CreateRecord
{
    protected static string $resource = PengajuanPiketResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
