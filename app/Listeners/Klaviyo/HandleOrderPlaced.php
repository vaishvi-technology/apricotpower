<?php

namespace App\Listeners\Klaviyo;

use App\Events\Klaviyo\OrderPlaced;
use App\Jobs\Klaviyo\TrackKlaviyoEvent;
use App\Models\Customer;

class HandleOrderPlaced
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $customer = $order->customer_id ? Customer::find($order->customer_id) : null;

        if (!$customer || !$customer->email) {
            return;
        }

        $items = $order->lines->map(function ($line) {
            return [
                'ProductName' => $line->description,
                'Quantity' => $line->quantity,
                'UnitPrice' => $line->unit_price?->value ? $line->unit_price->value / 100 : 0,
                'LineTotal' => $line->sub_total?->value ? $line->sub_total->value / 100 : 0,
            ];
        })->toArray();

        $orderTotal = $order->total?->value ? $order->total->value / 100 : 0;

        TrackKlaviyoEvent::dispatch(
            'Placed Order',
            $customer->email,
            [
                'OrderId' => $order->id,
                'OrderReference' => $order->reference ?? $order->id,
                'OrderTotal' => $orderTotal,
                'ItemCount' => $order->lines->count(),
                'Items' => $items,
                'Currency' => $order->currency?->code ?? 'USD',
            ],
            $orderTotal,
        );
    }
}
