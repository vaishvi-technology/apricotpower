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
        Schema::create('inventory_logs', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Log details
            $table->datetime('log_date')->nullable();
            $table->string('log_user', 100)->nullable();
            $table->longText('log_entry')->nullable();

            // Foreign keys
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->unsignedBigInteger('cart_id')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('item_id');
            $table->index('cart_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
