<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;

class LunarSetupSeeder extends Seeder
{
    /**
     * Seed the Lunar base data required for products.
     */
    public function run(): void
    {
        // Create default Language (required by Lunar's UrlGenerator)
        // Using firstOrCreate to avoid Blink cache issues
        Language::firstOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'default' => true,
            ]
        );

        // Create default Currency
        Currency::firstOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'US Dollar',
                'exchange_rate' => 1,
                'decimal_places' => 2,
                'default' => true,
                'enabled' => true,
            ]
        );

        // Create default TaxClass
        TaxClass::firstOrCreate(
            ['name' => 'Default'],
            ['default' => true]
        );

        // Create default ProductType
        ProductType::firstOrCreate(
            ['name' => 'Default']
        );

        // Create default AttributeGroup for products
        AttributeGroup::firstOrCreate(
            ['handle' => 'product-details'],
            [
                'attributable_type' => 'product',
                'name' => ['en' => 'Product Details'],
                'position' => 1,
            ]
        );
    }
}
