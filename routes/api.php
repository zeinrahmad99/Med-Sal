<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // Permission
    include __DIR__ . '/developers/Api/V1/Permission.php';

    // Role
    include __DIR__ . '/developers/Api/V1/Role.php';

});

// Auth
include __DIR__ . '/developers/Api/V1/Auth.php';

// Service
include __DIR__ . '/developers/Api/V1/Service.php';

// Product
include __DIR__ . '/developers/Api/V1/Product.php';

// Cart
include __DIR__ . '/developers/Api/V1/Cart.php';

// User
include __DIR__ . '/developers/Api/V1/User.php';

// Category
include __DIR__ . '/developers/Api/V1/Category.php';

// Provider
include __DIR__ . '/developers/Api/V1/Provider.php';

// Appointment
include __DIR__ . '/developers/Api/V1/Appointment.php';

// Order
include __DIR__ . '/developers/Api/V1/Order.php';

// Admin
include __DIR__ . '/developers/Api/V1/Admin.php';

// App Key
include __DIR__ . '/developers/Api/V1/App-key.php';

// Notification
include __DIR__ . '/developers/Api/V1/Notification.php';

