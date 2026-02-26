<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Admin\Models\Staff;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed a super-admin staff for Lunar admin panel.
     */
    public function run(): void
    {
        Staff::updateOrCreate(
            ['email' => 'admin@apricotpower.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'admin' => true,
                'password' => 'password',
            ]
        );
    }
}
