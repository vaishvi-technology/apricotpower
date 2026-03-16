<?php

namespace App\Events\Klaviyo;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Order;

class OrderPlaced
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}
}
