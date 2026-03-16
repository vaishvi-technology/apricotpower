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
        $this->call(LunarSetupSeeder::class);
        $this->call(CountryStateSeeder::class);
        $this->call(DemoCustomerSeeder::class);
        $this->call(CustomerGroupSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(NutrientSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(HomeCollectionSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductCategorySeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(BlogCategorySeeder::class);
    }
}
