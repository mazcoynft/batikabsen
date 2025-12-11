<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';
    protected $primaryKey = 'kode_cuti'; // Set primary key
    public $incrementing = false; // It's not auto-incrementing
    protected $keyType = 'string'; // The key is a string

    protected $fillable = [
        'kode_cuti',
        'nama_cuti',
        'jumlah_hari',
    ];

    public function pengajuanIzin(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class, 'cuti_id', 'id');
    }
}