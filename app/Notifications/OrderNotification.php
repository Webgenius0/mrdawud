<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order; // Store the order instance
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for the database.
     */
    public function toDatabase(object $notifiable): array
    {
        // Get the first product image from the order's items
        $productImage = $this->order->order_items->first()->product->image ?? 'default-product-image.jpg'; 

        // Create a custom message based on the status
        $statusMessage = $this->getStatusMessage($this->order->status);

        return [
            'order_status' => $this->order->status,
            'status_message' => $statusMessage, // The custom message
            'image' => $productImage,
        ];
    }

    /**
     * Get the status message based on the order status.
     */
    private function getStatusMessage($status)
    {
        switch ($status) {
            case 'ongoing':
                return 'Product is on the way'; // Custom message for ongoing status
            case 'completed':
                return 'Product collected';
            case 'canceled':
                return 'Your order has been canceled';
            default:
                return 'Order status is unknown'; // Fallback message
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        // Get the first product image from the order's items
        $productImage = $this->order->order_items->first()->product->image ?? 'default-product-image.jpg'; 

        // Create a custom message based on the status
        $statusMessage = $this->getStatusMessage($this->order->status);

        return [
            'order_status' => $this->order->status,
            'status_message' => $statusMessage, // The custom message
            'image' => $productImage,
        ];
    }
}
