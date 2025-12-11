<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_notification', function (Blueprint $table) {
            $table->id();
            $table->string('bot_token')->nullable();
            $table->string('bot_username')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('notify_attendance')->default(true);
            $table->boolean('notify_leave_request')->default(true);
            $table->text('welcome_message')->nullable();
            $table->text('register_success_message')->nullable();
            $table->text('admin_register_success_message')->nullable();
            $table->timestamps();
        });
        
        Schema::create('telegram_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('notification_type'); // attendance, leave_request, etc
            $table->text('message');
            $table->boolean('is_sent')->default(false);
            $table->text('response')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_notification_logs');
        Schema::dropIfExists('telegram_notification');
    }
};