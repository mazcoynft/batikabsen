<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'nama',
        'no_hp',
        'jabatan',
        'kode_dept',
        'kode_cabang',
        'foto',
        'id_users',
        'nik',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'kode_dept', 'kode_dept');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function jamKerjaHarian()
    {
        return $this->hasMany(JamKerjaKaryawan::class, 'karyawan_id');
    }
    
    // Fungsi untuk mendapatkan jam kerja berdasarkan hari
    public function getJamKerjaByDay(string $day)
    {
        $jamKerja = $this->jamKerjaHarian()->where('hari', $day)->first();
        
        if ($jamKerja) {
            return $jamKerja->jamKerja();
        }
        
        // Jika tidak ada pengaturan khusus, gunakan jam kerja default
        return JamKerja::find($this->jam_kerja_id);
    }
    
    // Tambahkan relasi untuk pengajuan izin
    public function pengajuanIzin(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class, 'karyawan_id');
    }
}