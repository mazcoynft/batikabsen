<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';

    protected $fillable = [
        'nik',
        'nama',
        'tanggal_awal_lembur',
        'tanggal_akhir_lembur',
        'keterangan',
        'nama_lembaga',
        'status'
    ];

    protected $casts = [
        'tanggal_awal_lembur' => 'date',
        'tanggal_akhir_lembur' => 'date'
    ];

    // Relasi ke karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}