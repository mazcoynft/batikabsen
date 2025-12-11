<?php

namespace App\Filament\Resources\MonitoringPresensiResource\Pages;

use App\Filament\Resources\MonitoringPresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringPresensi extends EditRecord
{
    protected static string $resource = MonitoringPresensiResource::class;

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
