<?php

namespace App\Observers;

use App\Models\Customer;
use Lunar\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     * Updates last_order_at on the associated customer.
     */
    public function created(Order $order): void
    {
        if ($order->customer_id) {
            Customer::where('id', $order->customer_id)
                ->update(['last_order_at' => now()]);
        }
    }
}
