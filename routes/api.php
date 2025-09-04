<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OneCController;

Route::get('/ping', fn() => response()->json(['ok' => true]));

Route::get('/categories', [OneCController::class, 'categories']);
Route::get('/products', [OneCController::class, 'products']);
