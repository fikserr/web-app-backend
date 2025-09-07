<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OneCController;
use App\Http\Controllers\TelegramController;

Route::get('/ping', fn() => response()->json(['ok' => true]));

Route::get('/categories', [OneCController::class, 'categories']);
Route::get('/products', [OneCController::class, 'products']);
Route::post('/telegram-check', [TelegramController::class, 'checkTelegram']);
Route::post('/telegram/webhook', [App\Http\Controllers\TelegramBotController::class, 'webhook']);


