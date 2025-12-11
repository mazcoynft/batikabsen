<?php

namespace App\Filament\Resources\MonitoringPresensiResource\Pages;

use App\Filament\Resources\MonitoringPresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMonitoringPresensi extends CreateRecord
{
    protected static string $resource = MonitoringPresensiResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
