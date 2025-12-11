<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RekapCutiKaryawanExport implements FromCollection, WithHeadings, WithTitle, WithColumnFormatting
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Karyawan',
            'Departemen',
            'Jabatan',
            'Hak Cuti',
            'Cuti Terpakai',
            'Sisa Cuti',
            'Nominal Pengganti',
        ];
    }

    public function title(): string
    {
        return 'Rekap Cuti Karyawan';
    }
    
    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_CURRENCY_IDR,
        ];
    }
}