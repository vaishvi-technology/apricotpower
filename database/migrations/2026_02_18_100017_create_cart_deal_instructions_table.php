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
        Schema::create('cart_deal_instructions', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('deal_id')->constrained('cart_deals')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');

            // Instruction flags
            $table->boolean('do_add_item')->default(false);
            $table->boolean('do_same_item')->nullable();
            $table->integer('redeemable_count')->nullable();
            $table->boolean('do_item_discount')->default(false);
            $table->integer('discount_amount')->nullable();
            $table->boolean('do_cart_free_shipping')->default(false);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('deal_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_deal_instructions');
    }
};
