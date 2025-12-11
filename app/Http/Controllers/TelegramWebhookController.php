<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected $telegramService;
    
    public function __construct(TelegramBotService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    
    public function handleWebhook(Request $request)
    {
        $update = $request->all();
        
        Log::info('Telegram webhook received', ['update' => $update]);
        
        try {
            $this->telegramService->processCommand($update);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}