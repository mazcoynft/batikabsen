<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JamKerjaKaryawan extends Model
{
    use HasFactory;

    protected $table = 'jam_kerja_karyawan';
    
    protected $fillable = [
        'karyawan_id',
        'jam_kerja_id',
        'hari',
    ];
    
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
    
    public function jamKerja(): BelongsTo
    {
        return $this->belongsTo(JamKerja::class, 'jam_kerja_id');
    }
}