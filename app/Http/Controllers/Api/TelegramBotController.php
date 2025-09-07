<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function webhook(Request $request)
    {
        $update = $request->all();

        // foydalanuvchi chat id sini olish
        $chatId = $update['message']['chat']['id'] ?? null;

        Log::info("Telegramdan chat_id keldi: " . $chatId);

        return response()->json(['ok' => true]);
    }
}
