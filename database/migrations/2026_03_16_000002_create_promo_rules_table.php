<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained('promos')->cascadeOnDelete();
            $table->string('name');
            $table->integer('sort_order')->default(0);

            // Condition: Items in cart
            $table->boolean('cond_is_items')->default(false);
            $table->text('cond_item_list')->nullable();
            $table->boolean('cond_item_all')->default(false);
            $table->integer('cond_item_quantity')->default(1);

            // Condition: Subtotal
            $table->boolean('cond_is_subtotal')->default(false);
            $table->decimal('cond_subtotal_min', 10, 2)->default(0);
            $table->decimal('cond_subtotal_max', 10, 2)->default(0);

            // Condition: Weight
            $table->boolean('cond_is_weight')->default(false);
            $table->decimal('cond_weight_amount', 10, 2)->default(0);
            $table->boolean('cond_weight_greater_than')->default(true);

            // Action: Discount
            $table->boolean('act_is_discount')->default(false);
            $table->decimal('act_discount_amount', 10, 2)->default(0);
            $table->boolean('act_discount_is_percent')->default(false);
            $table->boolean('act_discount_is_for_items')->default(false);
            $table->text('act_discount_item_list')->nullable();
            $table->integer('act_discount_limit')->default(0);

            // Action: Free Shipping
            $table->boolean('act_is_free_shipping')->default(false);

            // Action: Free Items
            $table->boolean('act_is_free_items')->default(false);
            $table->boolean('act_item_is_all')->default(false);
            $table->text('act_item_list')->nullable();
            $table->integer('act_item_limit')->default(0);

            // Action: BOGO
            $table->boolean('act_is_bogo')->default(false);
            $table->text('act_bogo_item_list')->nullable();
            $table->integer('act_bogo_buy_count')->default(1);
            $table->integer('act_bogo_get_count')->default(1);
            $table->decimal('act_bogo_discount', 5, 2)->default(100);
            $table->integer('act_bogo_limit')->default(0);

            $table->timestamps();

            $table->index('promo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_rules');
    }
};
