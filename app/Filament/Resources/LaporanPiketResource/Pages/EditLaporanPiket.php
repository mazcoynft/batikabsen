<?php

namespace App\Filament\Resources\LaporanPiketResource\Pages;

use App\Filament\Resources\LaporanPiketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanPiket extends EditRecord
{
    protected static string $resource = LaporanPiketResource::class;

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
