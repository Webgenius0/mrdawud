<?php

namespace App\Http\Controllers\API\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetNotificationController extends Controller
{
    public function getUserNotifications()
{
    // Get the authenticated user
    $user = auth()->user();

    // Mark all unread notifications as read
    $user->unreadNotifications->markAsRead();

    // Get the user's notifications (latest first)
    $notifications = $user->notifications()->latest()->get();

    // Check if there are any notifications
    if ($notifications->isEmpty()) {
        return response()->json([
            'status' => 200,
            'message' => 'No notifications found',
            'notifications' => []
        ]);
    }

    // Return only status, message, and image from the notifications
    $formattedNotifications = $notifications->map(function ($notification) {
        // Get the order status
        $orderStatus = $notification->data['order_status'] ?? 'No status';

        // Determine the message based on the order status
        $statusMessage = '';
        if ($orderStatus == 'completed') {
            $statusMessage = 'Your order has been delivered!';
        } elseif ($orderStatus == 'ongoing') {
            $statusMessage = 'Your order is on the way!';
        } else {
            $statusMessage = 'Your order status has been updated to ' . $orderStatus;
        }

        return [
            'order_status' => $orderStatus,  // Access the status from the notification data
            'status_message' => $statusMessage,  // The custom message based on the status
            'image' => $notification->data['image'] ?? 'default-product-image.jpg',  // Fallback to default image if not set
        ];
    });

    return response()->json([
        'status' => 200,
        'message' => 'Notifications fetched successfully',
        'notifications' => $formattedNotifications,
    ]);
}

}