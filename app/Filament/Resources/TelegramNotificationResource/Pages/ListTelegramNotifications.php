<?php

namespace App\Filament\Resources\TelegramNotificationResource\Pages;

use App\Filament\Resources\TelegramNotificationResource;
use App\Models\TelegramNotification;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class ListTelegramNotifications extends ListRecords
{
    protected static string $resource = TelegramNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Test Webhook')
                ->color('primary')
                ->action(function () {
                    $config = TelegramNotification::first();
                    $token = $config?->bot_token;
                    if (!$token) {
                        Notification::make()
                            ->title('Webhook')
                            ->body('Bot token tidak ditemukan. Isi di Telegram Gateway atau .env')
                            ->danger()
                            ->send();
                        return;
                    }
                    try {
                        $resp = Http::withOptions(['verify' => false])->get("https://api.telegram.org/bot{$token}/getWebhookInfo");
                        $data = $resp->json();
                        $body = isset($data['result']) ? (
                            'URL: ' . ($data['result']['url'] ?? '-') . "\n" .
                            'Pending: ' . ($data['result']['pending_update_count'] ?? 0) . "\n" .
                            'Last Error: ' . ($data['result']['last_error_message'] ?? '-')
                        ) : 'Tidak ada data';
                        Notification::make()
                            ->title('Webhook Info')
                            ->body($body)
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Webhook Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('Set Webhook')
                ->color('success')
                ->action(function () {
                    $config = TelegramNotification::first();
                    $token = $config?->bot_token;
                    if (!$token) {
                        Notification::make()
                            ->title('Set Webhook')
                            ->body('Bot token tidak ditemukan. Isi di Telegram Gateway')
                            ->danger()
                            ->send();
                        return;
                    }
                    $base = Config::get('app.url');
                    $override = env('TELEGRAM_WEBHOOK_URL');
                    $url = ($override && trim($override) !== '') ? $override : rtrim($base, '/') . '/api/telegram/webhook';
                    $parts = parse_url($url);
                    if (!$parts || !isset($parts['scheme']) || strtolower($parts['scheme']) !== 'https' || in_array($parts['host'] ?? '', ['localhost','127.0.0.1'])) {
                        Notification::make()
                            ->title('Set Webhook')
                            ->body('URL webhook harus HTTPS dan publik. Atur TELEGRAM_WEBHOOK_URL di .env ke https://<domain>/api/telegram/webhook')
                            ->danger()
                            ->send();
                        return;
                    }
                    try {
                        Http::withOptions(['verify' => false])->get("https://api.telegram.org/bot{$token}/deleteWebhook");
                        $resp = Http::withOptions(['verify' => false])->get("https://api.telegram.org/bot{$token}/setWebhook", [
                            'url' => $url,
                            'allowed_updates' => ['message','callback_query'],
                            'drop_pending_updates' => true,
                        ]);
                        $json = $resp->json();
                        $ok = $json['ok'] ?? false;
                        $desc = $json['description'] ?? '';
                        Notification::make()
                            ->title('Set Webhook')
                            ->body(($ok ? 'Berhasil set webhook ke: ' : 'Gagal set webhook: ') . $url . ($desc ? "\n" . $desc : ''))
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Set Webhook Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}