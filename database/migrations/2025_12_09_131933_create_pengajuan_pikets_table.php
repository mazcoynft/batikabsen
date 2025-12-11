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
        Schema::create('pengajuan_pikets', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('nama_karyawan');
            $table->date('tanggal_awal_piket');
            $table->date('tanggal_akhir_piket');
            $table->string('jenis_piket'); // piket_mingguan, piket_khusus, hari_libur
            $table->integer('jumlah_hari');
            $table->integer('jumlah_hari_libur')->default(0);
            $table->decimal('nominal_piket', 10, 2);
            $table->text('keterangan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pikets');
    }
};
