<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CartController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/carts', [CartController::class, 'index']);
    Route::post('/carts', [CartController::class, 'store']);
    Route::delete('/carts/{id}', [CartController::class, 'delete']);
});