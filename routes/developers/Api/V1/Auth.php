<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register/patient', [AuthController::class, 'registerPatient']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register/provider', [AuthController::class, 'registerProvider']);