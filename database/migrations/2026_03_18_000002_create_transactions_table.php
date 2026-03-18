<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
                $table->string('type'); // charge, refund, void, capture
                $table->string('status'); // pending, completed, failed, cancelled
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->string('provider'); // authorize_net, stripe
                $table->string('provider_transaction_id')->nullable();
                $table->string('provider_response_code')->nullable();
                $table->string('provider_response_message')->nullable();
                $table->json('provider_metadata')->nullable();
                $table->string('last_four', 4)->nullable();
                $table->string('card_brand')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('user_id')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')
                      ->references('id')
                      ->on('lunar_customers')
                      ->nullOnDelete();

                $table->index('order_id');
                $table->index('customer_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
