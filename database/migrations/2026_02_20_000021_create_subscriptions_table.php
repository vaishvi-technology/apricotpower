<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('status')->default('active'); // active, paused, cancelled, expired
            $table->string('frequency'); // weekly, biweekly, monthly, quarterly, annually
            $table->integer('frequency_interval')->default(1);

            // Shipping address
            $table->string('shipping_first_name');
            $table->string('shipping_last_name');
            $table->string('shipping_company')->nullable();
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country')->default('US');
            $table->string('shipping_phone')->nullable();

            $table->date('next_order_date');
            $table->date('last_order_date')->nullable();
            $table->integer('orders_count')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();
            $table->index('customer_id');
            $table->index('status');
            $table->index('next_order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
