<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RekapPresensiHarianExport implements FromCollection, WithHeadings, WithTitle, WithColumnFormatting
{
    protected $data;
    protected $dateRange;

    public function __construct(array $data, array $dateRange)
    {
        $this->data = $data;
        $this->dateRange = $dateRange;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        $headings = [
            'NIK',
            'Nama Karyawan',
            'Bulan',
        ];
        
        // Tambahkan heading untuk setiap tanggal
        foreach ($this->dateRange as $date) {
            $headings[] = $date;
        }
        
        // Tambahkan heading untuk ringkasan
        $headings = array_merge($headings, [
            'H',
            'I',
            'S',
            'C',
            'A',
            'Terlambat',
            'Uang Makan',
        ]);
        
        return $headings;
    }

    public function title(): string
    {
        return 'Rekap Presensi';
    }
    
    public function columnFormats(): array
    {
        // Menentukan format untuk kolom uang makan (kolom terakhir)
        $lastColumn = chr(65 + count($this->headings()) - 1); // A + jumlah kolom - 1
        
        return [
            $lastColumn => NumberFormat::FORMAT_CURRENCY_IDR,
        ];
    }
}