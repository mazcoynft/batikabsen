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
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->integer('sisa_cuti_default')->nullable()->after('jumlah_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->dropColumn('sisa_cuti_default');
        });
    }
};