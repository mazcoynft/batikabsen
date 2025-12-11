// ... existing code ...

public function sendAttendanceNotificationToAdmin(Presensi $presensi, $rank)
{
    try {
        if (!$this->config || !$this->config->is_active || !$this->config->notify_attendance) {
            return false;
        }
        
        $karyawan = $presensi->karyawan;
        if (!$karyawan) {
            Log::error('Karyawan tidak ditemukan untuk notifikasi admin');
            return false;
        }
        
        $statusText = $presensi->status_presensi_in == '1' ? 'Tepat Waktu' : 'Terlambat';
        $jenisPresensi = ucfirst($presensi->jenis_presensi ?? 'normal');
        
        // Tambahkan emoji berdasarkan jenis presensi
        $emojiPresensi = match($presensi->jenis_presensi) {
            'onsite' => '🏢',
            'wfh' => '🏠',
            default => '✅'
        };
        
        $message = "{$emojiPresensi} <b>{$karyawan->nama}</b> telah melakukan absensi {$jenisPresensi}\n" .
                   "⏰ Waktu: {$presensi->jam_in} WIB\n" .
                   "📍 Status: {$statusText}\n" .
                   "🔢 Urutan: #{$rank}";
        
        // Kirim ke semua admin
        $admins = User::whereNotNull('id_admin_telegram')->get();
        
        foreach ($admins as $admin) {
            $this->sendMessage($admin->id_admin_telegram, $message);
            
            // Log notifikasi
            TelegramNotificationLog::create([
                'user_id' => $admin->id,
                'notification_type' => 'admin_attendance',
                'message' => $message,
                'is_sent' => true
            ]);
        }
        
        return true;
    } catch (\Exception $e) {
        Log::error('Error saat mengirim notifikasi admin: ' . $e->getMessage());
        return false;
    }
}

public function sendAttendanceNotification($user, $presensi, $rank)
{
    try {
        if (!$this->config || !$this->config->is_active || !$this->config->notify_attendance) {
            return false;
        }
        
        if (!$user->id_chat_telegram) {
            return false;
        }
        
        $karyawan = $presensi->karyawan;
        if (!$karyawan) {
            return false;
        }
        
        $statusText = $presensi->status_presensi_in == '1' ? 'Tepat Waktu ✅' : 'Terlambat ⚠️';
        $jenisPresensi = ucfirst($presensi->jenis_presensi ?? 'normal');
        
        // Tambahkan emoji berdasarkan jenis presensi
        $emojiPresensi = match($presensi->jenis_presensi) {
            'onsite' => '🏢',
            'wfh' => '🏠',
            default => '✅'
        };
        
        $message = "{$emojiPresensi} Absensi {$jenisPresensi} Berhasil!\n\n" .
                   "👤 Nama: {$karyawan->nama}\n" .
                   "⏰ Waktu: {$presensi->jam_in} WIB\n" .
                   "📍 Status: {$statusText}\n" .
                   "🔢 Urutan: #{$rank}";
        
        $this->sendMessage($user->id_chat_telegram, $message);
        
        // Log notifikasi
        TelegramNotificationLog::create([
            'user_id' => $user->id,
            'notification_type' => 'attendance',
            'message' => $message,
            'is_sent' => true
        ]);
        
        // Kirim notifikasi ke admin
        $this->sendAttendanceNotificationToAdmin($presensi, $rank);
        
        return true;
    } catch (\Exception $e) {
        Log::error('Error saat mengirim notifikasi absensi: ' . $e->getMessage());
        return false;
    }
}

// ... existing code ...