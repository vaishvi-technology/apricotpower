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
        Schema::create('nutrition_fact_line_item_options', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Option details
            $table->string('name', 250)->nullable();
            $table->string('display_title', 250)->nullable();
            $table->string('display_class', 250)->nullable();
            $table->longText('description')->nullable();

            // Display settings
            $table->integer('rank')->nullable();
            $table->boolean('is_funky')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_fact_line_item_options');
    }
};
