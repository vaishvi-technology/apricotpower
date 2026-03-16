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
        Schema::table(config('lunar.database.table_prefix') . 'customer_groups', function (Blueprint $table) {
            // Minimum products required in cart to qualify for group pricing
            $table->integer('products_minimum')->default(1)->after('minimum_order_amount');

            // Default tier method for new pricing tiers
            $table->boolean('default_tier_by_quantity')->default(true)->after('products_minimum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('lunar.database.table_prefix') . 'customer_groups', function (Blueprint $table) {
            $table->dropColumn(['products_minimum', 'default_tier_by_quantity']);
        });
    }
};
