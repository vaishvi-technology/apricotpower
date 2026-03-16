<?php

namespace App\Listeners\Klaviyo;

use App\Events\Klaviyo\SubscriptionChanged;
use App\Jobs\Klaviyo\UpdateKlaviyoSubscription;

class HandleSubscriptionChanged
{
    public function handle(SubscriptionChanged $event): void
    {
        UpdateKlaviyoSubscription::dispatch(
            $event->customer->email,
            $event->subscribed,
        );
    }
}
