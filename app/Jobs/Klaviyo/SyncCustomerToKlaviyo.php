<?php

namespace App\Jobs\Klaviyo;

use App\Models\Customer;
use App\Services\KlaviyoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCustomerToKlaviyo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Customer $customer,
        public bool $subscribeToList = false,
    ) {}

    public function handle(KlaviyoService $klaviyo): void
    {
        $profileAttributes = [
            'email' => $this->customer->email,
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
        ];

        if ($this->customer->phone) {
            $profileAttributes['phone_number'] = $this->customer->phone;
        }

        if ($this->customer->company_name) {
            $profileAttributes['organization'] = $this->customer->company_name;
        }

        $klaviyo->upsertProfile($profileAttributes);

        if ($this->subscribeToList) {
            $klaviyo->subscribeToList($this->customer->email);
        }
    }
}
