<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained('promos')->cascadeOnDelete();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->boolean('free_shipping')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('promo_id');
            $table->index('customer_id');
            $table->index('customer_email');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_usages');
    }
};
