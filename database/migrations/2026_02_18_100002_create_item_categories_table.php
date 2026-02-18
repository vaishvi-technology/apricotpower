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
        Schema::create('item_categories', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Basic info
            $table->string('title', 100)->nullable();
            $table->string('image', 50)->nullable();
            $table->longText('description')->nullable();

            // Organization
            $table->boolean('is_hidden')->default(false);
            $table->integer('rank')->default(9999);

            // SEO / Meta fields
            $table->string('meta_title', 255)->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('meta_keywords')->nullable();

            // Open Graph
            $table->string('og_title', 255)->nullable();
            $table->string('og_type', 50)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->string('og_url', 255)->nullable();

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
        Schema::dropIfExists('item_categories');
    }
};
