<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AdminController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admins', [AdminController::class, 'index']);
    Route::post('/admin/{id}', [AdminController::class, 'create']);
    Route::get('/admin/{id}', [AdminController::class, 'show']);
    Route::delete('/admin/{id}', [AdminController::class, 'delete']);
    Route::get('/index/admin/permissions', [AdminController::class, 'indexAdminPermissions']);
    Route::get('/index/provider/permissions', [AdminController::class, 'indexProviderPermissions']);



});

