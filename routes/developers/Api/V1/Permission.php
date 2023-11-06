<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PermissionController;

Route::get('/permissions', [PermissionController::class, 'index']);

Route::put('/permissions/{id}', [PermissionController::class, 'update']);
