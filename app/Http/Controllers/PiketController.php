<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPiket;
use App\Models\Karyawan;
use App\Models\TelegramNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PiketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('id_users', $user->id)->first();
        
        if (!$karyawan) {
            return redirect()->route('frontend.dashboard')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        // Get piket history
        $pikets = PengajuanPiket::where('nik', $karyawan->nik)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('frontend.piket.index', compact('pikets', 'karyawan'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_awal_piket' => 'required|date',
            'tanggal_akhir_piket' => 'required|date|after_or_equal:tanggal_awal_piket',
            'jenis_piket' => 'required|in:Mingguan,Hari Libur',
            'jumlah_hari' => 'required|integer|min:1',
            'nama_lembaga' => 'required|string',
            'keterangan' => 'required|string',
        ]);
        
        $user = Auth::user();
        $karyawan = Karyawan::where('id_users', $user->id)->first();
        
        if (!$karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan');
        }
        
        // Calculate jumlah hari from date range
        $tanggalAwal = new \DateTime($request->tanggal_awal_piket);
        $tanggalAkhir = new \DateTime($request->tanggal_akhir_piket);
        $interval = $tanggalAwal->diff($tanggalAkhir);
        $jumlahHari = $interval->days + 1; // +1 to include both start and end date
        
        // Calculate nominal based on jenis piket
        if ($request->jenis_piket === 'Hari Libur') {
            // Hari Libur: Rp 50.000/hari
            $nominal = $jumlahHari * 50000;
        } else {
            // Mingguan: Rp 25.000/hari
            $nominal = $jumlahHari * 25000;
        }
        
        // Create piket
        $piket = PengajuanPiket::create([
            'nik' => $karyawan->nik,
            'nama_karyawan' => $karyawan->nama,
            'tanggal_awal_piket' => $request->tanggal_awal_piket,
            'tanggal_akhir_piket' => $request->tanggal_akhir_piket,
            'jenis_piket' => $request->jenis_piket,
            'jumlah_hari' => $jumlahHari,
            'nominal_piket' => $nominal,
            'nama_lembaga' => $request->nama_lembaga,
            'keterangan' => $request->keterangan,
            'status' => 'pending'
        ]);
        
        // Send Telegram notification to employee
        $this->sendTelegramNotification(
            $user->id,
            $user->id_chat_telegram,
            "✅ *Pengajuan Piket Berhasil*\n\n" .
            "Nama: {$karyawan->nama}\n" .
            "Tanggal: " . date('d/m/Y', strtotime($request->tanggal_awal_piket)) . " - " . date('d/m/Y', strtotime($request->tanggal_akhir_piket)) . "\n" .
            "Jenis: {$request->jenis_piket}\n" .
            "Lembaga: {$request->nama_lembaga}\n" .
            "Nominal: Rp " . number_format($nominal, 0, ',', '.') . "\n" .
            "Status: ⏳ Pending\n\n" .
            "Menunggu persetujuan admin."
        );
        
        // Send Telegram notification to admin
        $this->sendAdminNotification(
            "🔔 *Pengajuan Piket Baru*\n\n" .
            "Dari: {$karyawan->nama} ({$karyawan->nik})\n" .
            "Tanggal: " . date('d/m/Y', strtotime($request->tanggal_awal_piket)) . " - " . date('d/m/Y', strtotime($request->tanggal_akhir_piket)) . "\n" .
            "Jenis: {$request->jenis_piket}\n" .
            "Lembaga: {$request->nama_lembaga}\n" .
            "Nominal: Rp " . number_format($nominal, 0, ',', '.') . "\n" .
            "Keterangan: {$request->keterangan}\n\n" .
            "⚠️ Segera lakukan tindakan: Setujui atau Tolak"
        );
        
        return back()->with('success', 'Pengajuan piket berhasil disubmit!');
    }
    
    private function sendTelegramNotification($userId, $chatId, $message)
    {
        if (!$chatId) {
            return;
        }
        
        try {
            $telegramSettings = TelegramNotification::first();
            
            if (!$telegramSettings || !$telegramSettings->is_active || !$telegramSettings->bot_token) {
                return;
            }
            
            Http::post("https://api.telegram.org/bot{$telegramSettings->bot_token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            // Log notifikasi
            \App\Models\TelegramNotificationLog::create([
                'user_id' => $userId,
                'notification_type' => 'piket',
                'message' => $message,
                'is_sent' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram notification failed: ' . $e->getMessage());
        }
    }
    
    private function sendAdminNotification($message)
    {
        try {
            $telegramSettings = TelegramNotification::first();
            
            if (!$telegramSettings || !$telegramSettings->is_active || !$telegramSettings->bot_token) {
                return;
            }
            
            // Send to all admin users who have registered their admin telegram
            $admins = User::whereNotNull('id_admin_telegram')->get();
            
            foreach ($admins as $admin) {
                Http::post("https://api.telegram.org/bot{$telegramSettings->bot_token}/sendMessage", [
                    'chat_id' => $admin->id_admin_telegram,
                    'text' => $message,
                    'parse_mode' => 'Markdown'
                ]);
                
                // Log notifikasi
                \App\Models\TelegramNotificationLog::create([
                    'user_id' => $admin->id,
                    'notification_type' => 'admin_piket',
                    'message' => $message,
                    'is_sent' => true
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Admin Telegram notification failed: ' . $e->getMessage());
        }
    }
}
