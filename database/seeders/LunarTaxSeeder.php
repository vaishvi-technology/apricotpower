<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;

class LunarTaxSeeder extends Seeder
{
    /**
     * Seed Lunar's tax configuration.
     * Required to prevent "Attempt to read property 'id' on null" errors.
     */
    public function run(): void
    {
        // Create default TaxClass if not exists
        $taxClass = TaxClass::firstOrCreate(
            ['name' => 'Default'],
            ['default' => true]
        );

        $this->command->info("TaxClass: {$taxClass->name} (ID: {$taxClass->id})");

        // Create default TaxZone if not exists
        $taxZone = TaxZone::firstOrCreate(
            ['name' => 'Default'],
            [
                'zone_type' => 'country',
                'price_display' => 'tax_inclusive',
                'default' => true,
                'active' => true,
            ]
        );

        $this->command->info("TaxZone: {$taxZone->name} (ID: {$taxZone->id})");

        // Create default TaxRate if not exists
        $taxRate = TaxRate::firstOrCreate(
            [
                'tax_zone_id' => $taxZone->id,
                'name' => 'Default Rate',
            ],
            ['priority' => 1]
        );

        $this->command->info("TaxRate: {$taxRate->name} (ID: {$taxRate->id})");

        // Create TaxRateAmount (0% tax) if not exists
        $taxRateAmount = TaxRateAmount::firstOrCreate(
            [
                'tax_rate_id' => $taxRate->id,
                'tax_class_id' => $taxClass->id,
            ],
            ['percentage' => 0]
        );

        $this->command->info("TaxRateAmount: {$taxRateAmount->percentage}% (ID: {$taxRateAmount->id})");

        $this->command->info('Lunar tax configuration seeded successfully!');
    }
}
