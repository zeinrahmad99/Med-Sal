<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProductController;

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/products/accepted/{id}', [ProductController::class, 'accepted']);
    Route::put('/product/pending/{id}', [ProductController::class, 'remove']);
    Route::post('/products', [ProductController::class, 'store']);
});
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'delete']);




