<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->foreignId('jam_kerja_id')->nullable()->constrained('jam_kerja')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropForeign(['jam_kerja_id']);
            $table->dropColumn('jam_kerja_id');
        });
    }
};