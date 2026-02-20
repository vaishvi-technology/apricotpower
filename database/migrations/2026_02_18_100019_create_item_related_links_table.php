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
        Schema::create('item_related_links', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('related_item_id')->constrained('items')->onDelete('cascade');

            // Ordering
            $table->integer('rank')->default(0);

            // Timestamps
            $table->timestamps();

            // Unique constraint
            $table->unique(['item_id', 'related_item_id']);

            // Indexes
            $table->index('item_id');
            $table->index('related_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_related_links');
    }
};
