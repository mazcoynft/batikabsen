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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik_app')->nullable();
            $table->string('pwd_app')->nullable();
            $table->string('id_chat_telegram')->nullable();
            $table->string('id_admin_telegram')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik_app', 'pwd_app', 'id_chat_telegram', 'id_admin_telegram']);
        });
    }
};