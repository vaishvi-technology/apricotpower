<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lunar_order_addresses', function (Blueprint $table) {
            // Middle name
            $table->string('middle_name', 1)->nullable()->after('first_name');

            // Alternate phone
            $table->string('alt_phone', 100)->nullable()->after('contact_phone');

            // Apartment/Suite
            $table->string('app_suite', 50)->nullable()->after('postcode');

            // Shipping same as billing flag
            $table->boolean('shipping_same')->default(false)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lunar_order_addresses', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'alt_phone', 'app_suite', 'shipping_same']);
        });
    }
};
