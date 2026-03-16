<?php

namespace App\Providers;

use App\Events\Klaviyo\AddedToCart;
use App\Events\Klaviyo\CustomerRegistered;
use App\Events\Klaviyo\OrderPlaced;
use App\Events\Klaviyo\StartedCheckout;
use App\Events\Klaviyo\SubscriptionChanged;
use App\Listeners\Klaviyo\HandleAddedToCart;
use App\Listeners\Klaviyo\HandleCustomerRegistered;
use App\Listeners\Klaviyo\HandleOrderPlaced;
use App\Listeners\Klaviyo\HandleStartedCheckout;
use App\Listeners\Klaviyo\HandleSubscriptionChanged;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CustomerRegistered::class => [
            HandleCustomerRegistered::class,
        ],
        OrderPlaced::class => [
            HandleOrderPlaced::class,
        ],
        SubscriptionChanged::class => [
            HandleSubscriptionChanged::class,
        ],
        AddedToCart::class => [
            HandleAddedToCart::class,
        ],
        StartedCheckout::class => [
            HandleStartedCheckout::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
