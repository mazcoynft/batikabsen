<?php

namespace App\Services;

use App\Models\TelegramNotification;
use App\Models\TelegramNotificationLog;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\PengajuanIzin;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TelegramBotService
{
    protected $config;
    
    public function __construct()
    {
        $this->config = TelegramNotification::first();
    }
    
    public function sendMessage($chatId, $message)
    {
        if (!$this->config || !$this->config->is_active || !$this->config->bot_token) {
            return false;
        }
        
        try {
            $client = Http::withOptions(config('app.env') === 'local' ? ['verify' => false] : []);
            $response = $client->post("https://api.telegram.org/bot{$this->config->bot_token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram notification error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function processCommand($message)
    {
        if (!isset($message['message']['text'])) {
            return;
        }
        
        $text = $message['message']['text'];
        $chatId = $message['message']['chat']['id'];
        
        if ($text === '/start') {
            return $this->sendMessage($chatId, $this->config->welcome_message ?? 
                "Selamat datang di BATIK App Bot! 👋\n\nSilahkan registrasi untuk mendapatkan notifikasi dari BatikApps dengan cara:\n\n/register karyawan@email.com");
        }
        
        if (strpos($text, '/register') === 0) {
            return $this->registerUser($chatId, $text);
        }
        
        if (strpos($text, '/regadmin') === 0) {
            return $this->registerAdmin($chatId, $text);
        }
        
        return $this->sendMessage($chatId, "Perintah tidak dikenali. Gunakan /start untuk informasi.");
    }
    
    public function registerUser($chatId, $text)
    {
        $parts = explode(' ', $text, 2);
        if (count($parts) !== 2) {
            return $this->sendMessage($chatId, "Format salah. Gunakan: /register email@example.com");
        }
        
        $email = trim($parts[1]);
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return $this->sendMessage($chatId, "Email tidak ditemukan dalam sistem.");
        }
        
        // Ambil data karyawan yang terkait dengan user
        $karyawan = Karyawan::where('id_users', $user->id)->first();
        $nama = $karyawan ? $karyawan->nama : $user->name;
        
        $user->id_chat_telegram = $chatId;
        $user->save();
        
        // Gunakan pesan dari konfigurasi jika ada, atau gunakan default
        if ($this->config && $this->config->register_success_message) {
            $successMessage = str_replace('{nama}', $nama, $this->config->register_success_message);
        } else {
            $successMessage = "✅ Berhasil mendaftarkan Telegram untuk karyawan: {$nama}\n\nAnda akan menerima notifikasi absensi dan pengajuan izin melalui bot ini.";
        }
        
        return $this->sendMessage($chatId, $successMessage);
    }
    
    public function registerAdmin($chatId, $text)
    {
        $parts = explode(' ', $text, 2);
        if (count($parts) !== 2) {
            return $this->sendMessage($chatId, "Format salah. Gunakan: /regadmin email@example.com");
        }
        
        $email = trim($parts[1]);
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return $this->sendMessage($chatId, "Email tidak ditemukan dalam sistem.");
        }
        
        $user->id_admin_telegram = $chatId;
        $user->save();
        
        // Gunakan pesan dari konfigurasi jika ada, atau gunakan default
        if ($this->config && $this->config->admin_register_success_message) {
            $successMessage = str_replace('{nama_admin}', $user->name, $this->config->admin_register_success_message);
        } else {
            $successMessage = "✅ Berhasil mendaftarkan Telegram untuk ADMIN: {$user->name}";
        }
        
        return $this->sendMessage($chatId, $successMessage);
    }
    
    public function sendAttendanceNotification(Presensi $presensi)
    {
        try {
            if (!$this->config || !$this->config->is_active || !$this->config->notify_attendance) {
                return false;
            }
            
            $karyawan = $presensi->karyawan;
            if (!$karyawan) {
                Log::error('Karyawan tidak ditemukan untuk presensi ID: ' . $presensi->id);
                return false;
            }
            
            $user = User::find($karyawan->id_users);
            
            if (!$user || !$user->id_chat_telegram) {
                return false;
            }
            
            // Hitung peringkat di leaderboard
            $rank = Presensi::whereDate('tgl_presensi', $presensi->tgl_presensi)
                ->whereNotNull('jam_in')
                ->orderBy('jam_in', 'asc')
                ->get()
                ->search(function($item) use ($presensi) {
                    return $item->id === $presensi->id;
                }) + 1;
            
            // Cek jenis presensi dengan prioritas pada jenis_presensi
            if ($presensi->jenis_presensi == 'wfh') {
                $message = "Terimakasih <b>{$karyawan->nama}</b> Anda telah melakukan absensi WFH (Check-in) pukul {$presensi->jam_in} wib. - No. {$rank}\nSelamat Bekerja!";
            } else if ($presensi->jenis_presensi == 'onsite') {
                $message = "Terimakasih <b>{$karyawan->nama}</b> Anda telah melakukan absensi Onsite (Check-in) pukul {$presensi->jam_in} wib. - No. {$rank}\nSelamat Bekerja!";
            } else {
                $statusText = $presensi->status_presensi_in == '1' ? 'Tepat Waktu' : 'Terlambat';
                $message = "Terimakasih <b>{$karyawan->nama}</b> Anda telah melakukan absensi (Check-in) pukul {$presensi->jam_in} wib. - {$statusText} No. {$rank}\nSelamat Bekerja!";
            }
            
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
            
            // Cek jenis presensi dengan prioritas pada jenis_presensi
            if ($presensi->jenis_presensi == 'wfh') {
                $message = "<b>{$karyawan->nama}</b> telah melakukan absensi WFH (Check-in) pukul {$presensi->jam_in} wib. - No. {$rank}";
            } else if ($presensi->jenis_presensi == 'onsite') {
                $message = "<b>{$karyawan->nama}</b> telah melakukan absensi Onsite (Check-in) pukul {$presensi->jam_in} wib. - No. {$rank}";
            } else {
                $statusText = $presensi->status_presensi_in == '1' ? 'Tepat Waktu' : 'Terlambat';
                $message = "<b>{$karyawan->nama}</b> telah melakukan absensi  (Check-in) pukul {$presensi->jam_in} wib. - {$statusText} No. {$rank}";
            }
            
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

    public function sendLeaveApprovalNotification(PengajuanIzin $pengajuan)
    {
        if (!$this->config || !$this->config->is_active || !$this->config->notify_leave_request) {
            return;
        }

        $karyawan = $pengajuan->karyawan;
        if (!$karyawan) return;
        
        $user = $karyawan->user;
        $approver = User::find($pengajuan->approved_by);

        $jenisIzin = $pengajuan->jenis_pengajuan;
        if ($jenisIzin == 'Cuti' && $pengajuan->cuti) {
            $jenisIzin = $pengajuan->cuti->nama_cuti;
        }

        $start = Carbon::parse($pengajuan->tanggal_awal);
        $end = Carbon::parse($pengajuan->tanggal_akhir);

        $tanggalFormatted = '';
        if ($start->isSameDay($end)) {
            $tanggalFormatted = $start->translatedFormat('d F Y');
        } else {
            $tanggalFormatted = $start->translatedFormat('d F') . ' - ' . $end->translatedFormat('d F Y');
        }

        // Message for Employee
        if ($user && $user->id_chat_telegram) {
            $messageToEmployee = "🔔 Pengajuan izin {$jenisIzin} anda\n" .
                "Tanggal : {$tanggalFormatted}\n" .
                "Keterangan : {$pengajuan->keterangan}\n" .
                "Jumlah hari : {$pengajuan->jumlah_hari}\n" .
                "Sisa cuti tahunan : {$pengajuan->sisa_cuti}\n" .
                "Berhasil disetujui. ✅";
            
            $this->sendMessage($user->id_chat_telegram, $messageToEmployee);
        }

        // Message for Admin who approved
        if ($approver && $approver->id_admin_telegram) {
            $messageToAdmin = "🔔 Pengajuan izin {$jenisIzin} Oleh <b>{$karyawan->nama}</b>\n" .
                "Tanggal : {$tanggalFormatted}\n" .
                "Keterangan : {$pengajuan->keterangan}\n" .
                "Jumlah hari : {$pengajuan->jumlah_hari}\n" .
                "Sisa cuti tahunan : {$pengajuan->sisa_cuti}\n" .
                "Berhasil disetujui. ✅ Untuk pembatalan cuti silahkan tekan tombol: \n" .
                "( ⛔️ BATALKAN ) diweb ABSENBATIK "; 

            $this->sendMessage($approver->id_admin_telegram, $messageToAdmin);
        }
    }

    public function sendLeaveRejectionNotification(PengajuanIzin $pengajuan)
    {
        if (!$this->config || !$this->config->is_active || !$this->config->notify_leave_request) {
            return;
        }

        $karyawan = $pengajuan->karyawan;
        if (!$karyawan) return;

        $user = $karyawan->user;

        $jenisIzin = $pengajuan->jenis_pengajuan;
        if ($jenisIzin == 'Cuti' && $pengajuan->cuti) {
            $jenisIzin = $pengajuan->cuti->nama_cuti;
        }

        $start = Carbon::parse($pengajuan->tanggal_awal);
        $end = Carbon::parse($pengajuan->tanggal_akhir);

        $tanggalFormatted = '';
        if ($start->isSameDay($end)) {
            $tanggalFormatted = $start->translatedFormat('d F Y');
        } else if ($start->isSameMonth($end) && $start->isSameYear($end)) {
            $tanggalFormatted = $start->format('d') . '-' . $end->format('d') . ' ' . $start->translatedFormat('F Y');
        } else {
            $tanggalFormatted = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
        }

        // Message for Employee
        if ($user && $user->id_chat_telegram) {
            $messageToEmployee = "🔔 Pengajuan izin {$jenisIzin} anda\n" .
            "Tanggal : {$tanggalFormatted}\n" .
            "Status: Ditolak Oleh admin ❌";
            
            $this->sendMessage($user->id_chat_telegram, $messageToEmployee);
        }
    }

    public function sendLeaveCancellationNotification(PengajuanIzin $pengajuan)
    {
        if (!$this->config || !$this->config->is_active || !$this->config->notify_leave_request) {
            return;
        }

        $karyawan = $pengajuan->karyawan;
        if (!$karyawan) return;

        $user = $karyawan->user;
        $admin = Auth::user();

        $jenisIzin = $pengajuan->jenis_pengajuan == 'Cuti' && $pengajuan->cuti ? $pengajuan->cuti->nama_cuti : $pengajuan->jenis_pengajuan;

        $start = Carbon::parse($pengajuan->tanggal_awal);
        $end = Carbon::parse($pengajuan->tanggal_akhir);
        $tanggalFormatted = $start->isSameDay($end) ? $start->translatedFormat('d F Y') : $start->format('d') . '-' . $end->format('d') . ' ' . $start->translatedFormat('F Y');

        // Message for Employee
        if ($user && $user->id_chat_telegram) {
            $messageToEmployee = "⛔️⛔️ Pengajuan izin {$jenisIzin} anda pada tanggal {$tanggalFormatted} dibatalkan oleh Admin.";
            $this->sendMessage($user->id_chat_telegram, $messageToEmployee);
        }

        // Message for Admin
        if ($admin && $admin->id_admin_telegram) {
            $messageToAdmin = "🔔Anda telah membatalkan pengajuan izin {$jenisIzin} untuk {$karyawan->nama} pada tanggal {$tanggalFormatted}.";
            $this->sendMessage($admin->id_admin_telegram, $messageToAdmin);
        }
    }
}