<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public $order;
    public $previousStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $previousStatus)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => "Status pesanan #{$this->order->id} telah diubah menjadi '{$this->order->status}'",
            'new_status' => $this->order->status,
            'prev_status' => $this->previousStatus,
        ];
    }
}
