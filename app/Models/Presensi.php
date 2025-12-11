<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    
    protected $fillable = [
        'karyawan_id', // Mengubah 'nik' menjadi 'karyawan_id'
        'tgl_presensi',
        'jam_in',
        'jam_out',
        'foto_in',
        'foto_out',
        'lokasi_in',
        'lokasi_out',
        'kode_jam_kerja',
        'status_presensi_in',
        'status_presensi_out',
        'status',
        'kode_izin',
        'keterangan',
        'jenis_presensi' // Tambahkan field jenis_presensi
    ];

    // Relasi ke model Karyawan
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id'); // Mengubah foreign key
    }

    // Relasi ke model JamKerja
    public function jamKerja(): BelongsTo
    {
        return $this->belongsTo(JamKerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }

    // Accessor untuk status presensi
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'h' => 'Hadir',
            's' => 'Sakit',
            'i' => 'Izin',
            'c' => 'Cuti',
            default => 'Tidak Diketahui'
        };
    }
}
