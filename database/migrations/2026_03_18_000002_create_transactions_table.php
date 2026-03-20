<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing table if it exists and recreate with all columns
        Schema::dropIfExists('transactions');

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('type');
            $table->string('status');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('provider');
            $table->string('provider_transaction_id')->nullable();
            $table->string('provider_response_code')->nullable();
            $table->string('provider_response_message')->nullable();
            $table->json('provider_metadata')->nullable();
            $table->string('last_four', 4)->nullable();
            $table->string('card_brand')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
