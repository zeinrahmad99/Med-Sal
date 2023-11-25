<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;


Route::post('/confirm-verification-code', [AuthController::class, 'confirmVerificationCode']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/refresh', [AuthController::class, 'refresh']);

Route::post('/change', [AuthController::class, 'changeLang']);
