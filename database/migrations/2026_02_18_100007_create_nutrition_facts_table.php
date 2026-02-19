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
        Schema::create('nutrition_facts', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to items
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');

            // Serving info
            $table->string('serving_size', 100)->nullable();
            $table->string('servings_per_container', 100)->nullable();

            // Calories
            $table->string('calories_per_serving', 100)->nullable();
            $table->string('calories_from_fat', 100)->nullable();

            // Flags
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_ingredients_enabled')->default(false);

            // Ingredients
            $table->longText('ingredients')->nullable();

            // Labels
            $table->integer('nutrition_label')->default(0);
            $table->integer('ingredients_label')->default(0);

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
        Schema::dropIfExists('nutrition_facts');
    }
};
