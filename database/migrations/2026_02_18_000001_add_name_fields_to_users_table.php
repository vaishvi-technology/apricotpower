<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the missing columns to the existing `users` table.
 *
 * Legacy `accounts` table column mapping:
 *   accounts.ShippingNameFirst → users.first_name
 *   accounts.ShippingNameLast  → users.last_name
 *   accounts.Company           → users.company_name
 *
 * users.name remains a regular varchar — it is set to
 * CONCAT(first_name, ' ', last_name) on create.
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
