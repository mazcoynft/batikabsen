<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piket', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('nama_karyawan');
            $table->date('tanggal_awal_piket');
            $table->date('tanggal_akhir_piket');
            $table->text('keterangan');
            $table->integer('jumlah_hari');
            $table->integer('jumlah_hari_libur')->default(0);
            $table->decimal('nominal_piket', 10, 2);
            $table->enum('jenis_piket', ['piket_mingguan', 'piket_khusus', 'hari_libur']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piket');
    }
};