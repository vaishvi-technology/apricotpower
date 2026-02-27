<?php

namespace App\Providers;

use App\Base\CustomMediaDefinitions;
use App\Lunar\CreateStaffPageExtension;
use App\Lunar\CustomerResourceExtension;
use App\Lunar\EditProductPageExtension;
use App\Lunar\EditStaffPageExtension;
use App\Lunar\ListProductsPageExtension;
use App\Lunar\ProductResourceExtension;
use App\Lunar\ProductTypeResourceExtension;
use App\Lunar\StaffResourceExtension;
use App\Modifiers\ShippingModifier;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;
use Lunar\Models\Order;
use Lunar\Admin\Filament\Resources\CustomerResource;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Filament\Resources\ProductResource\Pages\EditProduct;
use Lunar\Admin\Filament\Resources\ProductResource\Pages\ListProducts;
use Lunar\Filament\Resources\ProductTypeResource;
use Lunar\Admin\Filament\Resources\StaffResource;
use Lunar\Admin\Filament\Resources\StaffResource\Pages\CreateStaff;
use Lunar\Admin\Filament\Resources\StaffResource\Pages\EditStaff;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Telemetry;
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
            ProductTypeResource::class => ProductTypeResourceExtension::class,
            EditProduct::class => EditProductPageExtension::class,
            ListProducts::class => ListProductsPageExtension::class,
            CustomerResource::class => CustomerResourceExtension::class,
            StaffResource::class => StaffResourceExtension::class,
            EditStaff::class => EditStaffPageExtension::class,
            CreateStaff::class => CreateStaffPageExtension::class,
        ]);

        LunarPanel::panel(
            fn ($panel) => $panel
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\StaffLogin::class)
            ->passwordReset()
            ->authPasswordBroker('staff')
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
        // Disable Lunar telemetry to prevent DNS errors for stats.lunarphp.io
        Telemetry::optOut();

        $shippingModifiers->add(
            ShippingModifier::class
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Product::class,
            \App\Models\Product::class,
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\ProductVariant::class,
            \App\Models\ProductVariant::class,
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Tag::class,
            \App\Models\Tag::class,
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

        // Track last_order_at on customer when an order is created
        Order::observe(OrderObserver::class);
    }
}
