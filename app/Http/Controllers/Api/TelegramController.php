<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function saveUser(Request $request)
    {
        try {
            $user = \App\Models\TelegramUser::updateOrCreate(
                ['telegram_id' => $request->input('telegram_id')],
                [
                    'username'   => $request->input('username'),
                    'first_name' => $request->input('first_name'),
                    'last_name'  => $request->input('last_name'),
                ]
            );

            return response()->json(['ok' => true, 'user' => $user]);

        } catch (\Exception $e) {
            return response()->json([
                'ok'    => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
