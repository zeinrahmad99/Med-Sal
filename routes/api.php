<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {

    // TokenAuth
    include __DIR__ . '/developers/Api/V1/TokenAuth.php';
});

// Auth
include __DIR__ . '/developers/Api/V1/Auth.php';

// Service
include __DIR__ . '/developers/Api/V1/Service.php';

// Provider
include __DIR__ . '/developers/Api/V1/Provider.php';

// Product
include __DIR__ . '/developers/Api/V1/Product.php';

// Cart
include __DIR__ . '/developers/Api/V1/Cart.php';

// User
include __DIR__ . '/developers/Api/V1/User.php';

// Permission
include __DIR__ . '/developers/Api/V1/Permission.php';