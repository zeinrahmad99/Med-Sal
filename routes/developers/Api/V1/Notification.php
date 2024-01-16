<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NotificationController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('notifications/unread', [NotificationController::class, 'unreadNotifications']);
    Route::get('notifications/read', [NotificationController::class, 'readNotifications']);
    Route::get('notifications/{notificationId}', [NotificationController::class, 'getNotification']);
    Route::put('notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);    
    Route::delete('notifications/{notificationId}', [NotificationController::class, 'deleteNotification']);
});

