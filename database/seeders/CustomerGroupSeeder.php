<?php

namespace Database\Seeders;

use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    /**
     * Seed the customer groups based on legacy Apricot Power system.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Consumer',
                'handle' => 'consumer',
                'default' => true,
                'is_wholesale' => false,
                'description' => 'Default retail customers',
                'discount_percentage' => 0,
                'net_terms_eligible' => false,
                'net_terms_days' => 0,
                'minimum_order_amount' => null,
                'products_minimum' => 1,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Wholesale',
                'handle' => 'wholesale',
                'default' => false,
                'is_wholesale' => true,
                'description' => 'Wholesale accounts with volume pricing',
                'discount_percentage' => 0,
                'net_terms_eligible' => true,
                'net_terms_days' => 30,
                'minimum_order_amount' => 300.00,
                'products_minimum' => 1,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Distributor',
                'handle' => 'distributor',
                'default' => false,
                'is_wholesale' => true,
                'description' => 'Distributor accounts with deeper discounts',
                'discount_percentage' => 0,
                'net_terms_eligible' => true,
                'net_terms_days' => 30,
                'minimum_order_amount' => 500.00,
                'products_minimum' => 1,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Samples',
                'handle' => 'samples',
                'default' => false,
                'is_wholesale' => false,
                'description' => 'Sample and promotional orders',
                'discount_percentage' => 100,
                'net_terms_eligible' => false,
                'net_terms_days' => 0,
                'minimum_order_amount' => null,
                'products_minimum' => 1,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Marketing',
                'handle' => 'marketing',
                'default' => false,
                'is_wholesale' => false,
                'description' => 'Marketing and promotional pricing',
                'discount_percentage' => 0,
                'net_terms_eligible' => false,
                'net_terms_days' => 0,
                'minimum_order_amount' => null,
                'products_minimum' => 1,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Friends & Family',
                'handle' => 'friends-family',
                'default' => false,
                'is_wholesale' => false,
                'description' => 'Employee and affiliate discounts',
                'discount_percentage' => 0,
                'net_terms_eligible' => false,
                'net_terms_days' => 0,
                'minimum_order_amount' => null,
                'products_minimum' => 1,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Third-Party Marketplaces',
                'handle' => 'marketplaces',
                'default' => false,
                'is_wholesale' => false,
                'description' => 'Amazon, eBay, and other marketplace orders',
                'discount_percentage' => 0,
                'net_terms_eligible' => false,
                'net_terms_days' => 0,
                'minimum_order_amount' => null,
                'products_minimum' => 1,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Bulk',
                'handle' => 'bulk',
                'default' => false,
                'is_wholesale' => false,
                'description' => 'High-volume single orders',
                'discount_percentage' => 0,
                'net_terms_eligible' => false,
                'net_terms_days' => 0,
                'minimum_order_amount' => null,
                'products_minimum' => 1,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Private Label',
                'handle' => 'private-label',
                'default' => false,
                'is_wholesale' => true,
                'description' => 'White-label and private brand partners',
                'discount_percentage' => 0,
                'net_terms_eligible' => true,
                'net_terms_days' => 30,
                'minimum_order_amount' => 1000.00,
                'products_minimum' => 1,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($groups as $groupData) {
            CustomerGroup::updateOrCreate(
                ['handle' => $groupData['handle']],
                $groupData
            );
        }

        $this->command->info('Customer groups seeded successfully!');
    }
}
