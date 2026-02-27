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
        Schema::create('product_nutrients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nutrition_fact_id');
            $table->unsignedBigInteger('nutrient_id');
            $table->string('amount_per_serving')->nullable();
            $table->decimal('percent_daily_value', 5, 2)->nullable();
            $table->boolean('not_established')->default(false);
            $table->timestamps();

            $table->foreign('nutrition_fact_id')
                ->references('id')
                ->on('product_nutrition_facts')
                ->cascadeOnDelete();

            $table->foreign('nutrient_id')
                ->references('id')
                ->on('nutrients')
                ->cascadeOnDelete();

            $table->unique(['nutrition_fact_id', 'nutrient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_nutrients');
    }
};
