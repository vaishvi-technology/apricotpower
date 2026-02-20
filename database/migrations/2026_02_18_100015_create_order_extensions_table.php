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
        Schema::create('order_extensions', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Reference to Lunar orders (no FK constraint - external table)
            $table->unsignedBigInteger('order_id')->unique();

            // Order Status Flags
            $table->string('order_type', 50)->nullable();
            $table->boolean('closed')->default(false);
            $table->boolean('locked')->default(false);
            $table->boolean('cancelled')->default(false);
            $table->boolean('back_ordered')->default(false);
            $table->boolean('awaiting_payment')->default(false);
            $table->boolean('ship_ready')->default(false);

            // Payment
            $table->boolean('pmt_received')->default(false);
            $table->string('pmt_type', 50)->nullable();
            $table->longText('pmt_info')->nullable();
            $table->double('handling')->default(0);
            $table->double('credits_used')->default(0);
            $table->string('net30_pmt_type', 50)->nullable();
            $table->string('check_no', 50)->nullable();
            $table->integer('account')->nullable();
            $table->double('total_paid')->nullable();
            $table->double('credit_memo')->default(0);

            // Gift Card
            $table->double('gift_card_used')->default(0);
            $table->string('gift_card_number', 35)->nullable();
            $table->boolean('gift_card_applied')->default(false);
            $table->string('gift_card_auth_code', 50)->nullable();

            // Promo/Deals
            $table->integer('deal_id')->nullable();
            $table->boolean('deal_free_shipping')->default(false);
            $table->integer('promo_id')->nullable();
            $table->boolean('promo_is_referer')->default(false);
            $table->integer('promo_percent')->nullable();
            $table->double('promo_dollar')->nullable();
            $table->boolean('promo_free_shipping')->default(false);
            $table->string('ll_promo', 15)->nullable();
            $table->integer('ll_promo_points')->default(0);

            // Subscription
            $table->integer('sub_id')->nullable();
            $table->integer('sub_discount_percent')->nullable();
            $table->boolean('sub_is_original')->nullable();

            // Referral
            $table->string('referer', 150)->nullable();
            $table->integer('refer_id')->nullable();
            $table->integer('mail_list_msg_id')->nullable();

            // Admin
            $table->integer('admin_discount')->nullable();
            $table->integer('admin_id')->nullable();
            $table->boolean('admin_assist')->default(false);
            $table->longText('comments')->nullable();
            $table->integer('batch_id')->nullable();
            $table->boolean('limit_override')->default(false);

            // Notifications
            $table->boolean('pmt_notify')->default(false);
            $table->boolean('ship_notify')->default(false);
            $table->boolean('abandon_notified')->default(false);
            $table->string('pmt_reminder_sent', 10)->nullable();

            // Fraud/Security
            $table->tinyInteger('fraud_status')->unsigned()->default(0);
            $table->string('fraud_codes', 50)->nullable();
            $table->string('token', 25)->nullable();
            $table->string('ip', 50)->nullable();
            $table->string('trans_response', 10)->nullable();

            // Repeat Customer
            $table->boolean('is_repeat')->nullable();
            $table->boolean('repurchase_90')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('deal_id');
            $table->index('promo_id');
            $table->index('sub_id');
            $table->index('admin_id');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_extensions');
    }
};
