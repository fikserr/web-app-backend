<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OneCController;
use App\Http\Controllers\Api\TelegramController;

Route::get('/ping', fn() => response()->json(['ok' => true]));

Route::get('/categories', [OneCController::class, 'categories']);
Route::get('/products', [OneCController::class, 'products']);
Route::post('/telegram/save-user', [TelegramController::class, 'saveUser']);


