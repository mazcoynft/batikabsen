<?php

namespace App\Filament\Resources\LaporanPiketResource\Pages;

use App\Filament\Resources\LaporanPiketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanPiket extends CreateRecord
{
    protected static string $resource = LaporanPiketResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
