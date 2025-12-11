<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_pikets', function (Blueprint $table) {
            $table->dropColumn('jumlah_hari_libur');
            $table->string('nama_lembaga')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_pikets', function (Blueprint $table) {
            $table->integer('jumlah_hari_libur')->default(0)->after('jumlah_hari');
            $table->dropColumn('nama_lembaga');
        });
    }
};
