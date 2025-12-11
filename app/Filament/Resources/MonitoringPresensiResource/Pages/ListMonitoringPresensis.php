<?php

namespace App\Filament\Resources\MonitoringPresensiResource\Pages;

use App\Filament\Resources\MonitoringPresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonitoringPresensis extends ListRecords
{
    protected static string $resource = MonitoringPresensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Info')
                ->icon('heroicon-o-information-circle')
                ->color('info')
                ->modalHeading('Informasi Monitoring Presensi')
                ->modalDescription('
                    • Data menampilkan semua presensi karyawan (terbaru di atas)
                    • Gunakan filter tanggal untuk melihat data spesifik
                    • Status Terlambat (merah) = tidak dapat uang makan
                    • Status Tepat Waktu (hijau) = dapat uang makan
                    • Klik icon lokasi untuk melihat peta
                    • Data auto-refresh setiap 30 detik
                ')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
                
            Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    $this->resetTable();
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Data berhasil direfresh')
                        ->body('Data presensi telah diperbarui')
                        ->send();
                }),
        ];
    }
    
    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Tidak ada data presensi';
    }
    
    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Belum ada data presensi untuk tanggal yang dipilih. Pastikan karyawan sudah melakukan absensi.';
    }
    
    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-list';
    }
}
