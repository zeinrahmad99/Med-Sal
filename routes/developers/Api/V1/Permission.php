<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PermissionController;



Route::put('/permissions/{id}', [PermissionController::class, 'update']);
Route::middleware('language')->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index']);
});
