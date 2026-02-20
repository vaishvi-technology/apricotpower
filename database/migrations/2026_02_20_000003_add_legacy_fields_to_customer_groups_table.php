<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend Lunar's existing `customer_groups` table with legacy fields.
     *
     * Lunar's base customer_groups table has:
     *   id, name, handle, default, timestamps
     *
     * We add fields from legacy `groups` table and the ERD spec:
     *   discount_percentage, is_wholesale, net_terms_eligible, net_terms_days,
     *   minimum_order_amount, requires_approval, is_active, sort_order, description
     */
    public function up(): void
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->text('description')->nullable()->after('handle');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('description');
            $table->boolean('is_wholesale')->default(false)->after('discount_percentage');
            $table->boolean('net_terms_eligible')->default(false)->after('is_wholesale');
            $table->integer('net_terms_days')->default(0)->after('net_terms_eligible');
            $table->decimal('minimum_order_amount', 10, 2)->nullable()->after('net_terms_days');
            $table->boolean('requires_approval')->default(false)->after('minimum_order_amount');
            $table->boolean('is_active')->default(true)->after('requires_approval');
            $table->integer('sort_order')->default(0)->after('is_active');

            $table->index('is_active');
            $table->index('is_wholesale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_wholesale']);
            $table->dropColumn([
                'description', 'discount_percentage', 'is_wholesale',
                'net_terms_eligible', 'net_terms_days', 'minimum_order_amount',
                'requires_approval', 'is_active', 'sort_order',
            ]);
        });
    }
};
