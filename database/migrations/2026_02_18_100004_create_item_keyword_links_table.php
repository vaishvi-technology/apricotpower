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
        Schema::create('item_keyword_links', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->unsignedBigInteger('keyword_id'); // References external keywords table

            // Timestamps
            $table->timestamps();

            // Unique constraint
            $table->unique(['item_id', 'keyword_id']);

            // Index for keyword lookups
            $table->index('keyword_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_keyword_links');
    }
};
