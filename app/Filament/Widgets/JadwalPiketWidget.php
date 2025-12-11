<?php

namespace App\Filament\Widgets;

use App\Models\Piket;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class JadwalPiketWidget extends Widget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected static string $view = 'filament.widgets.jadwal-piket-widget';

    public function getData(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Ambil data piket bulan ini, group by minggu
        $piketData = Piket::whereMonth('tanggal_awal_piket', $currentMonth)
            ->whereYear('tanggal_awal_piket', $currentYear)
            ->where('jenis_piket', 'piket_mingguan')
            ->orderBy('tanggal_awal_piket', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal_awal_piket)->weekOfMonth;
            });
        
        $jadwalPiket = [];
        for ($minggu = 1; $minggu <= 4; $minggu++) {
            $piket = $piketData->get($minggu)?->first();
            $jadwalPiket[] = [
                'minggu' => $minggu,
                'nama' => $piket ? $piket->nama_karyawan : 'Belum ada'
            ];
        }
        
        return [
            'jadwal' => $jadwalPiket,
            'bulan' => Carbon::now()->locale('id')->translatedFormat('F Y')
        ];
    }
}