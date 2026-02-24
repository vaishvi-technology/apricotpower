<?php

namespace Database\Seeders;

use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    public function run(): void
    {
        CustomerGroup::updateOrCreate(
            ['handle' => 'consumer'],
            [
                'name' => 'Consumer',
                'is_wholesale' => false,
                'net_terms_eligible' => false,
                'is_active' => true,
                'default' => true,
            ]
        );

        CustomerGroup::updateOrCreate(
            ['handle' => 'wholesale'],
            [
                'name' => 'Wholesale',
                'is_wholesale' => true,
                'net_terms_eligible' => true,
                'is_active' => true,
                'default' => false,
            ]
        );

        CustomerGroup::updateOrCreate(
            ['handle' => 'distributor'],
            [
                'name' => 'Distributor',
                'is_wholesale' => false,
                'net_terms_eligible' => false,
                'is_active' => true,
                'default' => false,
            ]
        );

        CustomerGroup::updateOrCreate(
            ['handle' => 'friends-family'],
            [
                'name' => 'Friends & Family',
                'is_wholesale' => false,
                'net_terms_eligible' => false,
                'is_active' => true,
                'default' => false,
            ]
        );

        CustomerGroup::updateOrCreate(
            ['handle' => 'third-party-marketplaces'],
            [
                'name' => 'Third-Party Marketplaces',
                'is_wholesale' => true,
                'net_terms_eligible' => true,
                'is_active' => true,
                'default' => false,
            ]
        );

        CustomerGroup::updateOrCreate(
            ['handle' => 'admin'],
            [
                'name' => 'Admin',
                'is_wholesale' => false,
                'net_terms_eligible' => false,
                'is_active' => true,
                'default' => false,
            ]
        );
    }
}
