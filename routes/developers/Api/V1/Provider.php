<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProviderController;

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/provider/active/{id}', [ProviderController::class, 'approveProvider']);
    Route::put('/provider/blocked/{id}', [ProviderController::class, 'rejectProvider']);
    Route::post('/providers/{id}', [ProviderController::class, 'updateRequest']);
    Route::delete('/providers/{id}', [ProviderController::class, 'delete']);
    Route::get('/report/product/{provider}', [ProviderController::class, 'reportProduct']);
    Route::get('/report/service/{provider}', [ProviderController::class, 'reportService']);
    Route::get('/provider/{id}', [ProviderController::class, 'show']);
});


