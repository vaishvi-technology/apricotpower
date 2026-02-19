<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds storefront customer profile columns to the `users` table.
 *
 * Note: Admin/staff columns (is_locked, perm_edit_*, etc.) belong in
 * `lunar_staff` (managed via Filament). Do NOT add them here.
 *
 * Legacy `accounts` table column mapping (customers):
 *   accounts.ShippingNameFirst → first_name
 *   accounts.ShippingNameLast  → last_name
 *   accounts.Company           → company_name
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('company_name')->nullable()->after('last_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'company_name']);
        });
    }
};
