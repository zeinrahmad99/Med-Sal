<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //  Get all unread notifications for the authenticated user.
    public function unreadNotifications(Request $request)
    {
        try {
            $notifications = auth()->user()->unreadNotifications;
            $count = auth()->user()->unreadNotifications->count();
            return response()->json([
                'status' => 1,
                'notifications' => $notifications,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Get all read notifications for the authenticated user.
    public function readNotifications(Request $request)
    {
        try {
            $notifications = auth()->user()->readNotifications;
            $count = auth()->user()->readNotifications->count();
            return response()->json([
                'status' => 1,
                'notifications' => $notifications,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Mark a notification as read.
    public function markAsRead(Request $request, $notificationId)
    {
        try {
            $notification = auth()->user()->notifications->find($notificationId);
            $notification->markAsRead();
            return response()->json([
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Mark all notifications as read for the authenticated user.
    public function markAllAsRead(Request $request)
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json([
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Get the details of a specific notification
    public function getNotification(Request $request, $notificationId)
    {
        try {
            $notification = auth()->user()->notifications->find($notificationId);
            return response()->json([
                'status' => 1,
                'notification' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Delete a specific notification
    public function deleteNotification(Request $request, $notificationId)
    {
        try {
            $notification = auth()->user()->notifications->find($notificationId);
            $notification->delete();
            return response()->json([
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

}
