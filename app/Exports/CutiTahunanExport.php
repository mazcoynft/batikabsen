<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CutiTahunanExport implements FromCollection, WithHeadings, WithTitle
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
            'Tanggal Masuk',
            'Hak Cuti',
            'Cuti Terpakai',
            'Sisa Cuti',
            'Nominal Pengganti',
        ];
    }

    public function title(): string
    {
        return 'Laporan Cuti Tahunan';
    }
}