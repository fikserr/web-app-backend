<?php

use App\Http\Controllers\Api\BasketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OneCController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TelegramController;

Route::get('/ping', fn() => response()->json(['ok' => true]));

Route::get('/categories', [OneCController::class, 'categories']);
Route::get('/products', [OneCController::class, 'products']);
Route::post('/telegram/check', [TelegramController::class, 'checkTelegram']);
Route::post('/orders', [OrderController::class, 'store']);
// Basket list olish
Route::get('/basket/{userId}', [BasketController::class, 'list']);

// Basketga qo‘shish yoki kamaytirish (+/-)
Route::post('/basket/update', [BasketController::class, 'updateQuantity']);

// Basketdan butunlay o‘chirish (id orqali)
Route::delete('/basket/{id}', [BasketController::class, 'remove']);


