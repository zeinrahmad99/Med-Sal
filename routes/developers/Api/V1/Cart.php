<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CartController;

Route::get('/carts', [CartController::class, 'index']);
Route::post('/carts', [CartController::class, 'store']);
Route::delete('/carts/{id}', [CartController::class, 'delete']);