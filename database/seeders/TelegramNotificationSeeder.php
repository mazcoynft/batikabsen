<?php

namespace Database\Seeders;

use App\Models\TelegramNotification;
use Illuminate\Database\Seeder;

class TelegramNotificationSeeder extends Seeder
{
    public function run(): void
    {
        TelegramNotification::create([
            'bot_token' => '',
            'bot_username' => '',
            'is_active' => true,
            'notify_attendance' => true,
            'notify_leave_request' => true,
            'welcome_message' => "Selamat datang di BATIK App Bot! 👋\n\nSilahkan registrasi untuk mendapatkan notifikasi dari BatikApps dengan cara:\n\n/register karyawan@email.com",
            'register_success_message' => "✅ Berhasil mendaftarkan Telegram untuk karyawan: {nama_karyawan}\n\nAnda akan menerima notifikasi absensi dan pengajuan izin melalui bot ini.",
            'admin_register_success_message' => "✅ Berhasil mendaftarkan Telegram untuk ADMIN: {nama_admin}"
        ]);
    }
}