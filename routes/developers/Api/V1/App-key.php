<?php

use Illuminate\Support\Facades\Route;

// This route return app key
Route::get('/get-app-key', function () {
    return response()->json([
        'app_key' => env('APP_KEY')
    ]);
});