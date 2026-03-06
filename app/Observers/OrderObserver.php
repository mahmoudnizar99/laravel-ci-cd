<?php 

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderStatusUpdated;

class OrderObserver
{
    public function updated(Order $order)
    {
        if ($order->isDirty('status')) {
            $order->user->notify(new OrderStatusUpdated($order));
        }
    }
}