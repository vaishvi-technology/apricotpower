<?php

namespace App\Jobs\Klaviyo;

use App\Services\KlaviyoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackKlaviyoEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public string $metricName,
        public string $email,
        public array $properties = [],
        public ?float $value = null,
    ) {}

    public function handle(KlaviyoService $klaviyo): void
    {
        $klaviyo->trackEvent(
            $this->metricName,
            $this->email,
            $this->properties,
            $this->value,
        );
    }
}
