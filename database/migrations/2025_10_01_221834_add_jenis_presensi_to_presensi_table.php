<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->string('jenis_presensi')->default('normal')->after('status'); // normal, onsite, wfh
        });
    }

    public function down()
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn('jenis_presensi');
        });
    }
};