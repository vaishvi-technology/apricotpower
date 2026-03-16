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
        Schema::table('customer_group_prices', function (Blueprint $table) {
            // Tier method: true = by quantity, false = by cart total
            $table->boolean('is_by_quantity')->default(true)->after('price');

            // For cart-total based tiers, this is the dollar cutoff
            // For quantity-based tiers, min_quantity is used (already exists)
            $table->decimal('cutoff_amount', 10, 2)->nullable()->after('is_by_quantity');

            // Expiration date for sale pricing (primarily for Consumer group)
            $table->date('expires_at')->nullable()->after('cutoff_amount');

            // Whether this is the base group price (min_quantity=1, no tiers)
            $table->boolean('is_base_price')->default(false)->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_group_prices', function (Blueprint $table) {
            $table->dropColumn(['is_by_quantity', 'cutoff_amount', 'expires_at', 'is_base_price']);
        });
    }
};
