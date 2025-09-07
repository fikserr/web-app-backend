<?php
namespace App\Http\Controllers;

use App\Helpers\TelegramHelper;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function checkTelegram(Request $request)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $initData = $request->input('initData');

        parse_str($initData, $data);

        if (! isset($data['hash'])) {
            return response()->json(['ok' => false, 'error' => 'Hash topilmadi', 'raw' => $data]);
        }

        $hash = $data['hash'];
        unset($data['hash']);
        ksort($data);

        $data_check_string = collect($data)->map(function ($value, $key) {
            return $key . '=' . $value;
        })->implode("\n");

        $secretKey = hash('sha256', $botToken, true);
        $checkHash = hash_hmac('sha256', $data_check_string, $secretKey);

        if (hash_equals($checkHash, $hash)) {
            $userData = isset($data['user']) ? json_decode($data['user'], true) : null;

            if ($userData) {
                $user = \App\Models\TelegramUser::updateOrCreate(
                    ['telegram_id' => $userData['id']],
                    [
                        'username'   => $userData['username'] ?? null,
                        'first_name' => $userData['first_name'] ?? null,
                        'last_name'  => $userData['last_name'] ?? null,
                    ]
                );

                // ✅ Botga xabar yuboramiz
                $text = "Yangi foydalanuvchi:\n"
                    . "ID: {$user->telegram_id}\n"
                    . "Username: @" . ($user->username ?? '-') . "\n"
                    . "Ism: " . ($user->first_name ?? '-') . " " . ($user->last_name ?? '-');

                TelegramHelper::sendMessage(env('TELEGRAM_ADMIN_ID'), $text);

                return response()->json(['ok' => true, 'user' => $user]);
            } else {
                return response()->json(['ok' => true, 'message' => 'User kelmadi', 'data' => $data]);
            }
        }

        return response()->json(['ok' => false, 'error' => 'Telegram verification failed']);
    }
}
