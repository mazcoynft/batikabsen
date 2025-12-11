<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';
    
    protected $fillable = [
        'no_urut',
        'jenis_pengumuman',
        'isi_pengumuman',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active'
    ];
    
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    // Scope untuk mendapatkan pengumuman yang aktif saat ini
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                    ->where('tanggal_mulai', '<=', $now)
                    ->where('tanggal_selesai', '>=', $now)
                    ->orderBy('no_urut', 'asc');
    }
}