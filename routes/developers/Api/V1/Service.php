<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ServiceController;

Route::get('/services', [ServiceController::class, 'index']);
Route::post('/services', [ServiceController::class, 'store']);
Route::patch('/services/{id}', [ServiceController::class, 'update']);
Route::delete('/services/{id}', [ServiceController::class, 'delete']);