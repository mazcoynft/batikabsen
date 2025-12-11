<?php

namespace App\Filament\Resources\HariLiburResource\Pages;

use App\Filament\Resources\HariLiburResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHariLiburs extends ManageRecords
{
    protected static string $resource = HariLiburResource::class;

    // Hapus metode getTitle() yang menyebabkan error

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('generate')
                ->label('Generate Hari Libur')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Hari Libur')
                ->modalDescription('Apakah Anda yakin ingin mengambil data hari libur resmi Indonesia untuk tahun ini? Data yang sudah ada akan tetap dipertahankan.')
                ->modalSubmitActionLabel('Ya, Generate')
                ->action(function () {
                    $this->generateHariLibur();
                }),
        ];
    }
    
    protected function generateHariLibur()
    {
        try {
            $tahun = now()->year;
            
            // Ambil data dari API hari libur Indonesia (dayoffapi.vercel.app)
            $response = \Illuminate\Support\Facades\Http::get("https://dayoffapi.vercel.app/api");
            
            if (!$response->successful()) {
                \Filament\Notifications\Notification::make()
                    ->danger()
                    ->title('Gagal mengambil data')
                    ->body('Tidak dapat terhubung ke API hari libur.')
                    ->send();
                return;
            }
            
            $allHolidays = $response->json();
            
            // Cek apakah response valid
            if (!is_array($allHolidays)) {
                \Filament\Notifications\Notification::make()
                    ->danger()
                    ->title('Data tidak valid')
                    ->body('Format data dari API tidak sesuai.')
                    ->send();
                return;
            }
            
            $inserted = 0;
            $skipped = 0;
            
            // Filter hanya untuk tahun yang dipilih
            foreach ($allHolidays as $holiday) {
                // Parse tanggal untuk cek tahun
                $holidayYear = date('Y', strtotime($holiday['tanggal']));
                
                // Hanya ambil data untuk tahun yang dipilih
                if ($holidayYear == $tahun) {
                    // Cek apakah tanggal sudah ada
                    $exists = \App\Models\HariLibur::where('tanggal', $holiday['tanggal'])->exists();
                    
                    if (!$exists) {
                        \App\Models\HariLibur::create([
                            'tanggal' => $holiday['tanggal'],
                            'keterangan' => $holiday['keterangan'],
                        ]);
                        $inserted++;
                    } else {
                        $skipped++;
                    }
                }
            }
            
            \Filament\Notifications\Notification::make()
                ->success()
                ->title('Berhasil!')
                ->body("Berhasil menambahkan {$inserted} hari libur untuk tahun {$tahun}. {$skipped} data sudah ada sebelumnya.")
                ->send();
                
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Error')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->send();
        }
    }
}