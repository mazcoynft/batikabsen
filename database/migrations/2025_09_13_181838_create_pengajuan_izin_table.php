<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->char('kode_izin', 9)->primary();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->nullOnDelete();
            $table->date('tgl_izin_dari')->nullable();
            $table->date('tgl_izin_sampai')->nullable();
            $table->char('status', 1)->nullable()->comment('i: izin, s: sakit, c: cuti');
            $table->char('kode_cuti', 3)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->string('doc_sid', 255)->nullable();
            $table->char('status_approved', 1)->default('0')->comment('0: Pending, 1: Disetujui, 2: Ditolak');
            $table->char('sisa_cuti', 2)->nullable();
            $table->char('jumlah_hari', 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};