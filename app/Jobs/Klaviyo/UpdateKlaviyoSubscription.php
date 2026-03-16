<?php

namespace App\Jobs\Klaviyo;

use App\Services\KlaviyoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateKlaviyoSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public string $email,
        public bool $subscribe,
    ) {}

    public function handle(KlaviyoService $klaviyo): void
    {
        if ($this->subscribe) {
            $klaviyo->subscribeToList($this->email);
        } else {
            $klaviyo->unsubscribeFromList($this->email);
        }
    }
}
