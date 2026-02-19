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
        Schema::create('nutrition_fact_line_item_links', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('nutrition_fact_id')
                ->constrained('nutrition_facts')
                ->onDelete('cascade');
            $table->foreignId('nutrition_fact_line_item_option_id')
                ->constrained('nutrition_fact_line_item_options')
                ->onDelete('cascade');

            // Values
            $table->string('amount_per_serving', 50)->nullable();
            $table->double('percent_daily_value')->nullable();
            $table->boolean('is_not_established')->nullable();

            // Timestamps
            $table->timestamps();

            // Unique constraint
            $table->unique(
                ['nutrition_fact_id', 'nutrition_fact_line_item_option_id'],
                'nf_line_item_links_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_fact_line_item_links');
    }
};
