<?php

namespace App\Events\Klaviyo;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Cart;

class StartedCheckout
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Cart $cart,
        public string $email,
    ) {}
}
