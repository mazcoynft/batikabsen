<?php

namespace App\Filament\Resources\DokumenKaryawanResource\Pages;

use App\Filament\Resources\DokumenKaryawanResource;
use App\Models\DokumenKaryawan;
use App\Models\Karyawan;
use App\Models\TelegramNotification;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class CreateDokumenKaryawan extends CreateRecord
{
    protected static string $resource = DokumenKaryawanResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove temporary fields
        unset($data['karyawan_ids']);
        unset($data['kirim_semua']);
        
        return $data;
    }
    
    protected function handleRecordCreation(array $data): DokumenKaryawan
    {
        // Get form data before mutation
        $formData = $this->form->getState();
        $kirimSemua = $formData['kirim_semua'] ?? false;
        $karyawanIds = $formData['karyawan_ids'] ?? [];
        
        // Get karyawan list
        if ($kirimSemua) {
            $karyawans = Karyawan::all();
        } else {
            if (empty($karyawanIds)) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('Silakan pilih karyawan atau centang "Kirim ke Semua Karyawan"!')
                    ->send();
                    
                $this->halt();
            }
            $karyawans = Karyawan::whereIn('nik', $karyawanIds)->get();
        }
        
        if ($karyawans->isEmpty()) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Tidak ada karyawan yang ditemukan!')
                ->send();
                
            $this->halt();
        }
        
        $successCount = 0;
        $failCount = 0;
        
        // Create document for each karyawan
        foreach ($karyawans as $karyawan) {
            try {
                $dokumen = DokumenKaryawan::create([
                    'tipe' => $data['tipe'],
                    'judul' => $data['judul'],
                    'file_path' => $data['file_path'],
                    'nik' => $karyawan->nik,
                    'nama_karyawan' => $karyawan->nama,
                    'keterangan' => $data['keterangan'] ?? null,
                    'is_read' => false,
                ]);
                
                // Send Telegram notification to karyawan
                $this->sendTelegramToKaryawan($karyawan, $data['tipe'], $data['judul']);
                
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Failed to create document for ' . $karyawan->nama . ': ' . $e->getMessage());
                $failCount++;
            }
        }
        
        // Send notification to admin
        $this->sendTelegramToAdmin($data['tipe'], $data['judul'], $successCount);
        
        // Show notification
        Notification::make()
            ->success()
            ->title('Dokumen Berhasil Dikirim!')
            ->body("Berhasil: {$successCount} karyawan" . ($failCount > 0 ? " | Gagal: {$failCount}" : ""))
            ->send();
        
        // Return first created record (for redirect)
        return DokumenKaryawan::latest()->first();
    }
    
    private function sendTelegramToKaryawan($karyawan, $tipe, $judul)
    {
        $user = $karyawan->user;
        
        if (!$user || !$user->id_chat_telegram) {
            return;
        }
        
        try {
            $telegramSettings = TelegramNotification::first();
            
            if (!$telegramSettings || !$telegramSettings->is_active || !$telegramSettings->bot_token) {
                return;
            }
            
            $tipeName = $tipe === 'slip_gaji' ? 'Slip Gaji' : 'Dokumen';
            
            $message = "📄 *{$tipeName} Baru*\n\n" .
                       "Judul: {$judul}\n\n" .
                       "Admin mengirimkan file {$tipeName}. Silahkan dicek, terima kasih.";
            
            Http::post("https://api.telegram.org/bot{$telegramSettings->bot_token}/sendMessage", [
                'chat_id' => $user->id_chat_telegram,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            // Log notifikasi
            \App\Models\TelegramNotificationLog::create([
                'user_id' => $user->id,
                'notification_type' => 'dokumen_' . $tipe,
                'message' => $message,
                'is_sent' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram notification to karyawan failed: ' . $e->getMessage());
        }
    }
    
    private function sendTelegramToAdmin($tipe, $judul, $jumlahKaryawan)
    {
        try {
            $telegramSettings = TelegramNotification::first();
            
            if (!$telegramSettings || !$telegramSettings->is_active || !$telegramSettings->bot_token) {
                return;
            }
            
            $tipeName = $tipe === 'slip_gaji' ? 'Slip Gaji' : 'Dokumen';
            
            $message = "✅ *{$tipeName} Terkirim*\n\n" .
                       "Judul: {$judul}\n" .
                       "Jumlah Karyawan: {$jumlahKaryawan}\n\n" .
                       "File {$tipeName} sudah berhasil dikirim ke karyawan.";
            
            // Send to all admin users
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
                    'notification_type' => 'admin_dokumen_' . $tipe,
                    'message' => $message,
                    'is_sent' => true
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Telegram notification to admin failed: ' . $e->getMessage());
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
