<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function checkTelegram(Request $request)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        if (empty($botToken)) {
            return response()->json(['ok' => false, 'error' => 'Bot token .env da yo‘q']);
        }

        $initData = $request->input('initData');
        if (empty($initData)) {
            return response()->json(['ok' => false, 'error' => 'initData topilmadi'], 400);
        }

        // 1) initData parse qilish
        $parts = explode('&', rawurldecode($initData));
        $arr = [];
        foreach ($parts as $chunk) {
            $pair = explode('=', $chunk, 2);
            if (count($pair) === 2) {
                $arr[$pair[0]] = $pair[1];
            }
        }

        if (!isset($arr['hash'])) {
            return response()->json(['ok' => false, 'error' => 'Hash topilmadi'], 400);
        }

        $receivedHash = $arr['hash'];
        unset($arr['hash']);

        // 2) Data check string tayyorlash (Telegram formatida)
        ksort($arr);
        $dataCheckArr = [];
        foreach ($arr as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        $dataCheckString = implode("\n", $dataCheckArr);

        // 3) Secret key va hash hisoblash
        $secretKey = hash_hmac('sha256', $botToken, "WebAppData", true);
        $calculatedHash = bin2hex(hash_hmac('sha256', $dataCheckString, $secretKey, true));

        $isValid = hash_equals($calculatedHash, $receivedHash);

        // 4) auth_date tekshirish
        $authDateOk = true;
        if (isset($arr['auth_date'])) {
            $authDateOk = (time() - intval($arr['auth_date'])) <= 24 * 3600;
        }

        // 5) Muvaffaqiyatli bo'lsa
        if ($isValid && $authDateOk) {
            $userJson = isset($arr['user']) ? $arr['user'] : null;
            $userData = $userJson ? json_decode($userJson, true) : null;

            if ($userData) {
                $user = TelegramUser::updateOrCreate(
                    ['telegram_id' => $userData['id']],
                    [
                        'username' => $userData['username'] ?? null,
                        'first_name' => $userData['first_name'] ?? null,
                        'last_name' => $userData['last_name'] ?? null,
                        'language_code' => $userData['language_code'] ?? null,
                        'allows_write_to_pm' => $userData['allows_write_to_pm'] ?? false,
                        'photo_url' => $userData['photo_url'] ?? null,
                    ]
                );
            }

            return response()->json([
                'ok' => true,
                'user' => $userData ?? null,
            ]);
        }

        // Agar ishlamasa, boshqa formatni tekshiramiz
        return $this->tryAlternativeFormat($initData, $botToken, $receivedHash);
    }

    private function tryAlternativeFormat($initData, $botToken, $receivedHash)
    {
        // Alternative format: & bilan ajratish
        $parts = explode('&', $initData);
        $arr = [];
        foreach ($parts as $chunk) {
            $pair = explode('=', $chunk, 2);
            if (count($pair) === 2) {
                $arr[$pair[0]] = urldecode($pair[1]);
            }
        }

        unset($arr['hash']);
        if (isset($arr['signature'])) {
            unset($arr['signature']);
        }

        ksort($arr);
        $dataCheckArr = [];
        foreach ($arr as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        $dataCheckString = implode("&", $dataCheckArr);

        $secretKey = hash_hmac('sha256', $botToken, "WebAppData", true);
        $calculatedHash = bin2hex(hash_hmac('sha256', $dataCheckString, $secretKey, true));

        Log::warning('Alternative format check', [
            'data_check_string' => $dataCheckString,
            'calculated_hash' => $calculatedHash,
            'received_hash' => $receivedHash,
        ]);

        if (hash_equals($calculatedHash, $receivedHash)) {
            $userJson = isset($arr['user']) ? $arr['user'] : null;
            $userData = $userJson ? json_decode($userJson, true) : null;

            return response()->json([
                'ok' => true,
                'user' => $userData,
                'message' => 'Alternative format worked'
            ]);
        }

        return response()->json([
            'ok' => false,
            'error' => 'Telegram verification failed',
            'debug' => [
                'data_check_string' => $dataCheckString,
                'calculated_hash' => $calculatedHash,
                'received_hash' => $receivedHash,
            ],
        ], 400);
    }
}