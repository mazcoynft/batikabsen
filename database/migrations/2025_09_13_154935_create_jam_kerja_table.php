<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jam_kerja')->unique();
            $table->string('nama_jam_kerja');
            $table->time('awal_jam_masuk');
            $table->time('jam_masuk');
            $table->time('akhir_jam_masuk');
            $table->time('jam_pulang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_kerja');
    }
};