<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\RoleController;

Route::put('/roles/{role}', [RoleController::class, 'update']);

Route::middleware('language')->group(function () {
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
});
