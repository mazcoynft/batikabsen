<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapPresensiExport implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($row) {
            return [
                'nik' => $row['nik'],
                'nama' => $row['nama'],
                'tanggal' => $row['tanggal'],
                'jam_masuk' => $row['jam_masuk'],
                'jam_pulang' => $row['jam_pulang'],
                'terlambat' => $row['terlambat'],
                'pulang_cepat' => $row['pulang_cepat'],
                'jam_kerja' => $row['jam_kerja'],
                'status' => $row['status'],
                'uang_makan' => $row['uang_makan'],
                'keterangan' => $row['keterangan'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Karyawan',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Terlambat',
            'Pulang Cepat',
            'Jam Kerja',
            'Status',
            'Uang Makan',
            'Keterangan',
        ];
    }

    public function title(): string
    {
        return 'Laporan Rekap Presensi';
    }
}