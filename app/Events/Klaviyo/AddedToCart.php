<?php

namespace App\Events\Klaviyo;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddedToCart
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $email,
        public string $productName,
        public int $quantity,
        public ?float $price = null,
        public ?int $variantId = null,
    ) {}
}
