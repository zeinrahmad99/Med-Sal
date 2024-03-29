<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register/patient', [AuthController::class, 'registerPatient']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/provider', [AuthController::class, 'registerProvider']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/confirm-verification-code', [AuthController::class, 'confirmVerificationCode']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change', [AuthController::class, 'changeLang']);
});
