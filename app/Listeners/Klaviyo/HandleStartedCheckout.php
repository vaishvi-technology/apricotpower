<?php

namespace App\Listeners\Klaviyo;

use App\Events\Klaviyo\StartedCheckout;
use App\Jobs\Klaviyo\TrackKlaviyoEvent;

class HandleStartedCheckout
{
    public function handle(StartedCheckout $event): void
    {
        $cart = $event->cart;

        $items = $cart->lines->map(function ($line) {
            return [
                'ProductName' => $line->description,
                'Quantity' => $line->quantity,
                'LineTotal' => $line->subTotal?->value ? $line->subTotal->value / 100 : 0,
            ];
        })->toArray();

        $cartTotal = $cart->total?->value ? $cart->total->value / 100 : 0;

        TrackKlaviyoEvent::dispatch(
            'Started Checkout',
            $event->email,
            [
                'CartId' => $cart->id,
                'CartTotal' => $cartTotal,
                'ItemCount' => $cart->lines->count(),
                'Items' => $items,
            ],
            $cartTotal,
        );
    }
}
