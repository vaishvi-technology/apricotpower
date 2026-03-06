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
                'name' => 'Admin',
                'handle' => 'admin',
                'default' => false,
                'is_wholesale' => false,
                'description' => 'Admin orders',
                'discount_percentage' => 0,
                'net_terms_eligible' => false,
                'net_terms_days' => 0,
                'minimum_order_amount' => null,
                'products_minimum' => 1,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 9,
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
