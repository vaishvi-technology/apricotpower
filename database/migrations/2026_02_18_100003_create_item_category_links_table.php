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
        Schema::create('item_category_links', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('item_category_id')->constrained('item_categories')->onDelete('cascade');

            // Rank within category
            $table->integer('rank')->default(9999);

            // Timestamps
            $table->timestamps();

            // Unique constraint
            $table->unique(['item_id', 'item_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_category_links');
    }
};
