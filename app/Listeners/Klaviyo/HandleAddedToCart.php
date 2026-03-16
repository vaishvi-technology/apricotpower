<?php

namespace App\Listeners\Klaviyo;

use App\Events\Klaviyo\AddedToCart;
use App\Jobs\Klaviyo\TrackKlaviyoEvent;

class HandleAddedToCart
{
    public function handle(AddedToCart $event): void
    {
        TrackKlaviyoEvent::dispatch(
            'Added to Cart',
            $event->email,
            [
                'ProductName' => $event->productName,
                'Quantity' => $event->quantity,
                'Price' => $event->price,
                'VariantId' => $event->variantId,
            ],
            $event->price,
        );
    }
}
