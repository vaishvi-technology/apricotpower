<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // earn, redeem, bonus
            $table->string('action'); // order_placed, signup, review, birthday, etc.
            $table->decimal('points_per_dollar', 8, 2)->nullable();
            $table->integer('fixed_points')->nullable();
            $table->decimal('multiplier', 5, 2)->default(1);
            $table->decimal('redemption_value', 10, 4)->nullable();
            $table->integer('min_points_to_redeem')->nullable();
            $table->integer('max_points_per_order')->nullable();
            $table->foreignId('customer_group_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('action');
            $table->index('is_active');
            $table->index('customer_group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_rules');
    }
};
