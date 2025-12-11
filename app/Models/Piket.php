<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piket extends Model
{
    use HasFactory;

    protected $table = 'piket';

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'tanggal_awal_piket',
        'tanggal_akhir_piket',
        'keterangan',
        'jumlah_hari',
        'jumlah_hari_libur',
        'nominal_piket',
        'jenis_piket',
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