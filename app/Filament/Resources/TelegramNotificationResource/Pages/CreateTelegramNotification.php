<?php

namespace App\Filament\Resources\TelegramNotificationResource\Pages;

use App\Filament\Resources\TelegramNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTelegramNotification extends CreateRecord
{
    protected static string $resource = TelegramNotificationResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}