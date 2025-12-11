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
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->foreignId('jam_kerja_id')->constrained('jam_kerja')->onDelete('cascade');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->timestamps();
            
            // Memastikan kombinasi karyawan_id dan hari adalah unik
            $table->unique(['karyawan_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_kerja_karyawan');
    }
};