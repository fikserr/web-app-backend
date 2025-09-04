<?php

use App\Http\Controllers\Api\OneCController;

Route::get('/categories', [OneCController::class, 'categories']);
Route::get('/products', [OneCController::class, 'products']);