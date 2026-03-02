<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Address;
use Lunar\Models\Country;
use Lunar\Models\CustomerGroup;

class DemoCustomerSeeder extends Seeder
{
    /**
     * Seed demo customers with known credentials for testing.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $usCountry = Country::where('iso2', 'US')->first();

            if (! $usCountry) {
                $this->command->warn('US country not found. Run CountryStateSeeder first.');

                return;
            }

            $demoCustomers = [
                [
                    'data' => [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'email' => 'john@example.com',
                        'password' => 'password',
                        'phone' => '555-100-1001',
                        'company_name' => 'Doe Health Foods',

                    ],
                    'groups' => ['consumer'],
                ],
                [
                    'data' => [
                        'first_name' => 'Jane',
                        'last_name' => 'Smith',
                        'email' => 'jane@example.com',
                        'password' => 'password',
                        'phone' => '555-200-2002',
                        'company_name' => 'Smith Wellness Co.',
                    ],
                    'groups' => ['wholesale'],
                ],
                [
                    'data' => [
                        'first_name' => 'Robert',
                        'last_name' => 'Johnson',
                        'email' => 'robert@example.com',
                        'password' => 'password',
                        'phone' => '555-300-3003',
                        'company_name' => 'Johnson Naturals LLC',
                        'net_terms_approved' => true,
                        'credit_limit' => 5000.00,
                    ],
                    'groups' => ['distributor'],
                ],
            ];

            foreach ($demoCustomers as $entry) {
                $customer = Customer::create($entry['data']);

                // Attach customer groups by handle
                $groupIds = CustomerGroup::whereIn('handle', $entry['groups'])->pluck('id');
                $customer->customerGroups()->attach($groupIds);

                // Default shipping address
                Address::create([
                    'customer_id' => $customer->id,
                    'country_id' => $usCountry->id,
                    'title' => null,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'company_name' => $customer->company_name,
                    'line_one' => '123 Main St',
                    'city' => 'Los Angeles',
                    'state' => 'CA',
                    'postcode' => '90001',
                    'contact_phone' => $customer->phone,
                    'shipping_default' => true,
                    'billing_default' => true,
                ]);
            }
        });
    }
}
