<?php

namespace App\Listeners\Klaviyo;

use App\Events\Klaviyo\CustomerRegistered;
use App\Jobs\Klaviyo\SyncCustomerToKlaviyo;
use App\Jobs\Klaviyo\TrackKlaviyoEvent;

class HandleCustomerRegistered
{
    public function handle(CustomerRegistered $event): void
    {
        SyncCustomerToKlaviyo::dispatch(
            $event->customer,
            $event->subscribedToList,
        );

        TrackKlaviyoEvent::dispatch(
            'Customer Registered',
            $event->customer->email,
            [
                'FirstName' => $event->customer->first_name,
                'LastName' => $event->customer->last_name,
                'Email' => $event->customer->email,
                'SubscribedToList' => $event->subscribedToList,
                'Source' => 'website',
            ],
        );
    }
}
