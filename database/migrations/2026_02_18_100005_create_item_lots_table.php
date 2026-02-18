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
        Schema::create('item_lots', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to items
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');

            // Lot details
            $table->string('lot_number', 50);
            $table->date('expires_at');
            $table->integer('quantity')->default(0);
            $table->boolean('is_expired_notified')->default(false);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Index for expiration tracking
            $table->index('expires_at');
            $table->index(['item_id', 'lot_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_lots');
    }
};
