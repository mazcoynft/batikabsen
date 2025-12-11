<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateKaryawan extends CreateRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Buat user terlebih dahulu
        $user = User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => $data['password'] ?? null,
            'nik_app' => $data['nik'],
            'pwd_app' => $data['pwd_app'] ?? null, // Laravel akan otomatis mengenkripsi karena sudah didefinisikan di casts
            'id_chat_telegram' => $data['id_chat_telegram'] ?? null,
            'id_admin_telegram' => $data['status_users'] == 1 ? $data['id_admin_telegram'] : null,
            'status_users' => $data['status_users'],
        ]);

        // Hapus data yang tidak ada di model Karyawan
        unset($data['email']);
        unset($data['password']);
        unset($data['nik_app']);
        unset($data['pwd_app']);
        unset($data['id_chat_telegram']);
        unset($data['id_admin_telegram']);
        unset($data['status_users']);
        unset($data['name']);

        // Set id_users ke user yang baru dibuat
        $data['id_users'] = $user->id;

        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}