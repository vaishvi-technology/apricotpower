<?php

namespace App\Providers;

use App\Base\CustomMediaDefinitions;
use App\Lunar\CustomerResourceExtension;
use App\Lunar\EditProductPageExtension;
use App\Lunar\ProductResourceExtension;
use App\Lunar\StaffResourceExtension;
use App\Modifiers\ShippingModifier;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Filament\Resources\CustomerResource;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Filament\Resources\ProductResource\Pages\EditProduct;
use Lunar\Admin\Filament\Resources\StaffResource;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Base\ShippingModifiers;
use Lunar\Shipping\ShippingPlugin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::extensions([
            ProductResource::class => ProductResourceExtension::class,
            EditProduct::class => EditProductPageExtension::class,
            CustomerResource::class => CustomerResourceExtension::class,
            StaffResource::class => StaffResourceExtension::class,
        ]);

        LunarPanel::panel(
            fn ($panel) => $panel
            ->path('admin')
            ->plugins([
                new ShippingPlugin,
            ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
        )
            ->register();

        // Override media definitions to preserve PNG transparency
        $this->app['config']->set('lunar.media.definitions', [
            'asset' => CustomMediaDefinitions::class,
            'brand' => CustomMediaDefinitions::class,
            'collection' => CustomMediaDefinitions::class,
            'product' => CustomMediaDefinitions::class,
            'product-option' => CustomMediaDefinitions::class,
            'product-option-value' => CustomMediaDefinitions::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(
            ShippingModifier::class
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Product::class,
            \App\Models\Product::class,
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Customer::class,
            \App\Models\Customer::class,
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\CustomerGroup::class,
            \App\Models\CustomerGroup::class,
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Address::class,
            \App\Models\Address::class,
        );
    }
}
