<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $table = 'department';

    protected $fillable = [
        'kode_dept',
        'nama_dept',
    ];

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'kode_dept', 'kode_dept');
    }
}