<?php

namespace App\Filament\Resources\LaporanLemburResource\Pages;

use App\Filament\Resources\LaporanLemburResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanLembur extends EditRecord
{
    protected static string $resource = LaporanLemburResource::class;

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
