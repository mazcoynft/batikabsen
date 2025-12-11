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
            // Drop the foreign key constraint first
            $table->dropForeign(['kode_cuti']);
        });

        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Change kode_cuti column length from 3 to 8 characters
            $table->char('kode_cuti', 8)->nullable()->change();
        });

        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Recreate the foreign key constraint
            $table->foreign('kode_cuti')->references('kode_cuti')->on('cuti')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['kode_cuti']);
        });

        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Revert back to 3 characters
            $table->char('kode_cuti', 3)->nullable()->change();
        });

        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Recreate the foreign key constraint
            $table->foreign('kode_cuti')->references('kode_cuti')->on('cuti')->onUpdate('restrict')->onDelete('set null');
        });
    }
};