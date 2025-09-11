<?php

use App\Http\Controllers\Api\BasketController;
use App\Http\Controllers\Api\OneCController;

use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn() => response()->json(['ok' => true]));

Route::get('/categories', [OneCController::class, 'categories']);
Route::get('/products', [OneCController::class, 'products']);
Route::post('/telegram/check', [TelegramController::class, 'checkTelegram']);
// Basket list olish
Route::get('/basket/{userId}', [BasketController::class, 'list']);

// Basketga qo‘shish yoki kamaytirish (+/-)
Route::post('/basket/update', [BasketController::class, 'updateQuantity']);

// Basketdan butunlay o‘chirish (id orqali)
Route::delete('/basket/{id}', [BasketController::class, 'remove']);
Route::post('/orders', [OrderController::class, 'store']);
