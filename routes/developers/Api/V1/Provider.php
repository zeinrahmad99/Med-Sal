<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProviderController;

Route::delete('/providers/{id}', [ProviderController::class, 'delete']);
Route::post('/providers/{id}', [ProviderController::class, 'updateRequest']);