<?php

namespace App\AuthorizeNet;

use App\AuthorizeNet\Livewire\PaymentForm;
use App\AuthorizeNet\Managers\AuthorizeNetManager;
use App\AuthorizeNet\Services\CIMService;
use App\AuthorizeNet\Services\TransactionService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Facades\Payments;

class AuthorizeNetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            config_path('lunar/authorizenet.php'),
            'lunar.authorizenet'
        );

        $this->app->singleton(AuthorizeNetManager::class);

        $this->app->bind(CIMService::class, function ($app) {
            return new CIMService($app->make(AuthorizeNetManager::class));
        });

        $this->app->bind(TransactionService::class, function ($app) {
            return new TransactionService(
                $app->make(AuthorizeNetManager::class),
                $app->make(CIMService::class)
            );
        });
    }

    public function boot(): void
    {
        // Register payment type
        Payments::extend('authorizenet', function ($app) {
            return $app->make(AuthorizeNetPaymentType::class);
        });

        // Register Livewire component
        Livewire::component('authorizenet.payment-form', PaymentForm::class);

        // Blade directive for Accept.js script
        Blade::directive('authorizeNetScripts', function () {
            return "<?php echo '<script src=\"' . (config('lunar.authorizenet.environment') === 'production' ? config('lunar.authorizenet.accept_js.production_url') : config('lunar.authorizenet.accept_js.sandbox_url')) . '\"></script>'; ?>";
        });
    }
}
