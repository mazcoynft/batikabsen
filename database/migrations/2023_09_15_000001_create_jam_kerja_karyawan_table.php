<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_kerja_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('jam_kerja_id')->constrained('jam_kerja')->cascadeOnDelete();
            $table->string('hari', 10); // senin, selasa, rabu, dst
            $table->timestamps();
            
            // Unique constraint untuk memastikan tidak ada duplikasi hari untuk karyawan yang sama
            $table->unique(['karyawan_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_kerja_karyawan');
    }
};