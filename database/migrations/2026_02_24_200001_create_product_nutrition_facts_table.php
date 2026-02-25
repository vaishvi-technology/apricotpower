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
        Schema::create('product_nutrition_facts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique();
            $table->string('serving_size')->nullable();
            $table->string('servings_per_container')->nullable();
            $table->string('calories_per_serving')->nullable();
            $table->string('calories_from_fat')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->longText('ingredients')->nullable();
            $table->boolean('ingredients_enabled')->default(false);
            $table->string('label_type')->default('nutrition');
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on((new \Lunar\Models\Product)->getTable())
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_nutrition_facts');
    }
};
