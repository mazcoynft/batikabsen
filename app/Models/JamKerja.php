<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JamKerja extends Model
{
    use HasFactory;

    protected $table = 'jam_kerja';
    
    protected $fillable = [
        'kode_jam_kerja',
        'nama_jam_kerja',
        'awal_jam_masuk',
        'jam_masuk',
        'akhir_jam_masuk',
        'jam_pulang',
    ];

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'jam_kerja_id');
    }
}
