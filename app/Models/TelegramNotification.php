<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramNotification extends Model
{
    use HasFactory;
    
    protected $table = 'telegram_notification';
    
    protected $fillable = [
        'bot_token',
        'bot_username',
        'is_active',
        'notify_attendance',
        'notify_leave_request',
        'welcome_message',
        'register_success_message',
        'admin_register_success_message'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'notify_attendance' => 'boolean',
        'notify_leave_request' => 'boolean',
    ];
}