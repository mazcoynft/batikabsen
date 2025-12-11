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
            // Drop the existing primary key on 'kode_izin'
            $table->dropPrimary();

            // Add a new auto-incrementing 'id' column as the primary key
            $table->id()->first();

            // Modify 'kode_izin' to be a unique string (it's no longer primary)
            $table->string('kode_izin')->unique()->change();

            // Change column types to integer for calculations
            $table->integer('jumlah_hari')->nullable()->change();
            $table->integer('sisa_cuti')->nullable()->change();

            // Update comment for 'status_approved' to include the 'Dibatalkan' state
            $table->string('status_approved', 1)->default('0')->comment('0:Menunggu, 1:Disetujui, 2:Ditolak, 3:Dibatalkan')->change();

            // Add foreign key constraint for 'kode_cuti'
            $table->foreign('kode_cuti')->references('kode_cuti')->on('cuti')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['kode_cuti']);

            // Revert 'status_approved' comment
            $table->char('status_approved', 1)->default('0')->comment('0: Pending, 1: Disetujui, 2: Ditolak')->change();

            // Revert column types back to char
            $table->char('jumlah_hari', 2)->nullable()->change();
            $table->char('sisa_cuti', 2)->nullable()->change();

            // Drop the 'id' column and its primary key
            $table->dropColumn('id');

            // Restore 'kode_izin' as the primary key
            $table->char('kode_izin', 9)->primary()->change();
        });
    }
};