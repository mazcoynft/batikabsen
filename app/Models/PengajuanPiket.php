<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPiket extends Model
{
    protected $table = 'pengajuan_pikets';

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'tanggal_awal_piket',
        'tanggal_akhir_piket',
        'jenis_piket',
        'jumlah_hari',
        'nominal_piket',
        'nama_lembaga',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tanggal_awal_piket' => 'date',
        'tanggal_akhir_piket' => 'date',
        'nominal_piket' => 'decimal:2'
    ];

    // Relasi ke karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
