<?php

namespace App\Filament\Resources\LaporanLemburResource\Pages;

use App\Filament\Resources\LaporanLemburResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanLembur extends CreateRecord
{
    protected static string $resource = LaporanLemburResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
