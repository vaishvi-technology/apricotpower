<?php

namespace App\Events\Klaviyo;

use App\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public bool $subscribedToList = false,
    ) {}
}
