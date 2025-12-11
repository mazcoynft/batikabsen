<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'lokasi',
        'radius',
    ];

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'kode_cabang', 'kode_cabang');
    }

    // Helper untuk mendapatkan latitude dan longitude
    public function getLatitudeAttribute()
    {
        $coordinates = explode(',', $this->lokasi);
        return trim($coordinates[0] ?? '');
    }

    public function getLongitudeAttribute()
    {
        $coordinates = explode(',', $this->lokasi);
        return trim($coordinates[1] ?? '');
    }
}