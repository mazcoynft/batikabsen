<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokumen_karyawans', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['slip_gaji', 'dokumen']); // slip_gaji atau dokumen
            $table->string('judul');
            $table->string('file_path');
            $table->string('nik');
            $table->string('nama_karyawan');
            $table->text('keterangan')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_karyawans');
    }
};
