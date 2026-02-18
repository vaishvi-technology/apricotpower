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
        Schema::create('item_prices', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->unsignedBigInteger('group_id'); // References external groups table

            // Pricing rules
            $table->boolean('is_by_quantity')->default(true);
            $table->double('cutoff')->default(0);
            $table->double('price')->default(0);

            // Expiration
            $table->date('ends_at')->nullable();
            $table->boolean('is_expire_alert_sent')->default(false);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('group_id');
            $table->index('ends_at');
            $table->index(['item_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_prices');
    }
};
