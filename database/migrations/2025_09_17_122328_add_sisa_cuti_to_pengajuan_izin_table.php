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
        $table->integer('sisa_cuti')->nullable()->after('jumlah_hari');
    });
}

public function down(): void
{
    Schema::table('pengajuan_izin', function (Blueprint $table) {
        $table->dropColumn('sisa_cuti');
    });
}
};
