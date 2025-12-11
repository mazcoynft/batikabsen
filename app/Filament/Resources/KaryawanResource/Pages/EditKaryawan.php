<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditKaryawan extends EditRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        if ($user) {
            $data['email'] = $user->email;
            $data['nik_app'] = $user->nik_app;
            $data['pwd_app'] = $user->pwd_app;
            $data['id_chat_telegram'] = $user->id_chat_telegram;
            $data['id_admin_telegram'] = $user->id_admin_telegram;
            $data['status_users'] = $user->status_users ?? 2;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = $record->user;

        if ($user) {
            $userData = [
                'name' => $data['nama'],
                'email' => $data['email'],
                'nik_app' => $data['nik'],
                'pwd_app' => $data['pwd_app'] ?? $user->pwd_app, // Gunakan nilai lama jika tidak diubah
                'id_chat_telegram' => $data['id_chat_telegram'] ?? null,
                'status_users' => $data['status_users'],
            ];

            if ($data['status_users'] == 1) {
                $userData['id_admin_telegram'] = $data['id_admin_telegram'] ?? null;
            } else {
                $userData['id_admin_telegram'] = null;
            }

            if (isset($data['password']) && !empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            $user->update($userData);
        }

        // Hapus data yang tidak ada di model Karyawan
        unset($data['email']);
        unset($data['password']);
        unset($data['nik_app']);
        unset($data['pwd_app']);
        unset($data['id_chat_telegram']);
        unset($data['id_admin_telegram']);
        unset($data['status_users']);
        unset($data['name']);

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}