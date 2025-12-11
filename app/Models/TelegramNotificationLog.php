<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramNotificationLog extends Model
{
    use HasFactory;
    
    protected $table = 'telegram_notification_logs';
    
    protected $fillable = [
        'user_id',
        'notification_type',
        'message',
        'is_sent',
        'response'
    ];
    
    protected $casts = [
        'is_sent' => 'boolean',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}