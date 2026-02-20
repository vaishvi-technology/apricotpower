<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            // Lunar already has: id, name, handle, default, timestamps
            // Adding our custom fields
            $table->text('description')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->boolean('is_wholesale')->default(false);
            $table->boolean('net_terms_eligible')->default(false);
            $table->integer('net_terms_days')->default(0);
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'discount_percentage', 'is_wholesale',
                'net_terms_eligible', 'net_terms_days', 'minimum_order_amount',
                'requires_approval', 'is_active', 'sort_order'
            ]);
        });
    }
};
