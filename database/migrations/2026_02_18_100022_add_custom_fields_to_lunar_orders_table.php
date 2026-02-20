<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Lunar already has: id, user_id, customer_id, channel_id, status, reference, customer_reference,
            // sub_total, discount_total, shipping_total, tax_breakdown, tax_total, total,
            // notes, currency_code, compare_currency_code, exchange_rate, placed_at, meta, timestamps

            // Adding our custom fields
            $table->foreignId('subscription_id')->nullable();
            $table->string('payment_status')->default('pending');

            // Shipping address
            $table->string('shipping_first_name')->nullable();
            $table->string('shipping_last_name')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('shipping_address_line_1')->nullable();
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_country')->default('US');
            $table->string('shipping_phone')->nullable();

            // Billing address
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_company')->nullable();
            $table->string('billing_address_line_1')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->default('US');
            $table->string('billing_phone')->nullable();
            $table->string('billing_email')->nullable();

            // Additional fields
            $table->string('coupon_code')->nullable();
            $table->string('shipping_method')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->date('payment_due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_id', 'payment_status',
                'shipping_first_name', 'shipping_last_name', 'shipping_company',
                'shipping_address_line_1', 'shipping_address_line_2', 'shipping_city',
                'shipping_state', 'shipping_postal_code', 'shipping_country', 'shipping_phone',
                'billing_first_name', 'billing_last_name', 'billing_company',
                'billing_address_line_1', 'billing_address_line_2', 'billing_city',
                'billing_state', 'billing_postal_code', 'billing_country', 'billing_phone',
                'billing_email', 'coupon_code', 'shipping_method',
                'customer_notes', 'admin_notes', 'ip_address', 'user_agent',
                'payment_due_date', 'paid_at', 'deleted_at'
            ]);
        });
    }
};
