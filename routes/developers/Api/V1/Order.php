<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OrderController;

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/order/approve/{id}',[OrderController::class,'approveOrder']);
    Route::put('/order/reject/{id}',[OrderController::class,'rejectOrder']);
});
Route::get('/orders',[OrderController::class,'index']);
Route::post('/order', [OrderController::class, 'store']);
Route::put('/order/{id}', [OrderController::class, 'update']);
Route::delete('/order/{id}', [OrderController::class, 'delete']);
Route::get('/order/{id}',[OrderController::class,'show']);
