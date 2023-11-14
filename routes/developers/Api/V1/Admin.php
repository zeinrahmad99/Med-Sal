<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AdminController;


Route::middleware('auth:sanctum')->group(function () {
Route::get('/admins', [AdminController::class, 'index']);
Route::post('/admin/{id}', [AdminController::class, 'create']);

Route::get('/roles/{id}', [RoleController::class, 'show']);
Route::put('/roles/{role}', [RoleController::class, 'update']);

});



