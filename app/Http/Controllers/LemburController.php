<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Karyawan;
use App\Models\TelegramNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LemburController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('id_users', $user->id)->first();
        
        if (!$karyawan) {
            return redirect()->route('frontend.dashboard')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        // Get lembur history
        $lemburs = Lembur::where('nik', $karyawan->nik)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('frontend.lembur.index', compact('lemburs', 'karyawan'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_awal_lembur' => 'required|date',
            'tanggal_akhir_lembur' => 'required|date|after_or_equal:tanggal_awal_lembur',
            'nama_lembaga' => 'required|string|max:255',
            'keterangan' => 'required|string',
        ]);
        
        $user = Auth::user();
        $karyawan = Karyawan::where('id_users', $user->id)->first();
        
        if (!$karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan');
        }
        
        // Create lembur
        $lembur = Lembur::create([
            'nik' => $karyawan->nik,
            'nama' => $karyawan->nama,
            'tanggal_awal_lembur' => $request->tanggal_awal_lembur,
            'tanggal_akhir_lembur' => $request->tanggal_akhir_lembur,
            'nama_lembaga' => $request->nama_lembaga,
            'keterangan' => $request->keterangan,
            'status' => 'pending'
        ]);
        
        // Send Telegram notification to employee
        $this->sendTelegramNotification(
            $user->id,
            $user->id_chat_telegram,
            "✅ *Pengajuan Lembur Berhasil*\n\n" .
            "Nama: {$karyawan->nama}\n" .
            "Tanggal: " . date('d/m/Y', strtotime($request->tanggal_awal_lembur)) . " - " . date('d/m/Y', strtotime($request->tanggal_akhir_lembur)) . "\n" .
            "Lembaga: {$request->nama_lembaga}\n" .
            "Keterangan: {$request->keterangan}\n" .
            "Status: ⏳ Pending\n\n" .
            "Menunggu persetujuan admin."
        );
        
        // Send Telegram notification to admin
        $this->sendAdminNotification(
            "🔔 *Pengajuan Lembur Baru*\n\n" .
            "Dari: {$karyawan->nama} ({$karyawan->nik})\n" .
            "Tanggal: " . date('d/m/Y', strtotime($request->tanggal_awal_lembur)) . " - " . date('d/m/Y', strtotime($request->tanggal_akhir_lembur)) . "\n" .
            "Lembaga: {$request->nama_lembaga}\n" .
            "Keterangan: {$request->keterangan}\n\n" .
            "⚠️ Segera lakukan tindakan: Setujui atau Tolak"
        );
        
        return back()->with('success', 'Pengajuan lembur berhasil disubmit!');
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
                'notification_type' => 'lembur',
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
                    'notification_type' => 'admin_lembur',
                    'message' => $message,
                    'is_sent' => true
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Admin Telegram notification failed: ' . $e->getMessage());
        }
    }
}
