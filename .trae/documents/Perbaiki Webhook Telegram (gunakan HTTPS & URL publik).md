## Akar Masalah
- Aksi “Set Webhook” menggunakan `APP_URL` saat ini: `http://localhost:8000`, yang ditolak oleh Telegram (wajib HTTPS dan URL publik).
- `getWebhookInfo` menampilkan `URL:` kosong → webhook belum terset.

## Rencana Perubahan Kode
1. Perbaiki konstruksi URL webhook di halaman admin:
   - Gunakan `TELEGRAM_WEBHOOK_URL` dari `.env` jika tersedia; jika tidak, fallback ke `APP_URL`.
   - Validasi: tolak jika URL bukan HTTPS atau host `localhost` dan tampilkan notifikasi yang menjelaskan syarat HTTPS.
   - Tampilkan detail dari response Telegram (`ok`, `description`) agar penyebab kegagalan terlihat.
   - File: `app/Filament/Resources/TelegramNotificationResource/Pages/ListTelegramNotifications.php` (aksi “Set Webhook”).
2. Pertahankan bypass SSL `verify=false` hanya untuk lingkungan `local` agar tidak terhalang CA Windows saat dev.

## Langkah Konfigurasi (tanpa kode)
1. Set `.env`:
   - `APP_URL=https://<domain-ngrok-anda>`
   - `TELEGRAM_WEBHOOK_URL=https://<domain-ngrok-anda>/api/telegram/webhook`
   - Pastikan `TELEGRAM_BOT_TOKEN` sudah benar.
2. Muat ulang konfigurasi aplikasi (restart server atau `php artisan config:clear && config:cache`).

## Proses Set Ulang Webhook
1. Dari admin “Telegram Gateway”, klik “Set Webhook”.
2. Pastikan notifikasi menampilkan “Berhasil set webhook ke: https://<domain-ngrok-anda>/api/telegram/webhook”.
3. Klik “Test Webhook” dan cek:
   - `URL` terisi.
   - `Pending: 0`.
   - `Last Error: -`.

## Validasi Fungsional
- Kirim `/start` ke bot. Bot harus membalas pesan welcome.
- Jika tidak:
  - Cek `storage/logs/laravel.log` untuk “Telegram webhook received”.
  - Cek `getWebhookInfo` → bila error, perbaiki domain/HTTPS.

## Catatan
- Telegram tidak menerima `http://localhost`. Untuk dev, gunakan ngrok atau domain publik dengan SSL.
- Setelah CA lokal diperbaiki, kita bisa menghapus `verify=false` di production agar verifikasi SSL ketat tetap berlaku.