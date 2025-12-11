<?php

namespace App\Filament\Resources\PengajuanPiketResource\Pages;

use App\Filament\Resources\PengajuanPiketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanPiket extends EditRecord
{
    protected static string $resource = PengajuanPiketResource::class;

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
