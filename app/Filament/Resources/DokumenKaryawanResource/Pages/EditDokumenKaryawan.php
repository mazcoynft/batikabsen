<?php

namespace App\Filament\Resources\DokumenKaryawanResource\Pages;

use App\Filament\Resources\DokumenKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDokumenKaryawan extends EditRecord
{
    protected static string $resource = DokumenKaryawanResource::class;

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
