<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('tgl_presensi');
            $table->time('jam_in')->nullable();
            $table->time('jam_out')->nullable();
            $table->string('foto_in', 255)->nullable();
            $table->string('foto_out', 255)->nullable();
            $table->text('lokasi_in')->nullable();
            $table->text('lokasi_out')->nullable();
            $table->char('kode_jam_kerja', 4)->nullable();
            $table->char('status_presensi_in', 1)->nullable();
            $table->char('status_presensi_out', 1)->nullable();
            $table->char('status', 1)->nullable();
            $table->char('kode_izin', 9)->nullable();
            $table->timestamps();
            
            // Foreign key ke tabel jam_kerja
            $table->foreign('kode_jam_kerja')->references('kode_jam_kerja')->on('jam_kerja')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};