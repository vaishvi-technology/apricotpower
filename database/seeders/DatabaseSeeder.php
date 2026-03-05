<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core Lunar setup (Currency, Channel, Language, TaxClass)
        $this->call(LunarSetupSeeder::class);

        // Tax configuration (TaxZone, TaxRate, TaxRateAmount)
        $this->call(LunarTaxSeeder::class);

        // Customer groups for pricing tiers
        $this->call(CustomerGroupSeeder::class);

        // Admin user
        $this->call(SuperAdminSeeder::class);

        // Product organization
        $this->call(TagSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductCategorySeeder::class);
    }
}
