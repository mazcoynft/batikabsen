<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenKaryawan extends Model
{
    protected $table = 'dokumen_karyawans';

    protected $fillable = [
        'tipe',
        'judul',
        'file_path',
        'nik',
        'nama_karyawan',
        'keterangan',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    // Relasi ke karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
