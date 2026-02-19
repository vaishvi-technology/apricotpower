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
        Schema::create('incoming_inventories', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to items
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');

            // Incoming inventory details
            $table->integer('quantity');
            $table->date('due_date')->nullable();
            $table->string('tracking_url', 200)->nullable();
            $table->longText('notes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_inventories');
    }
};
