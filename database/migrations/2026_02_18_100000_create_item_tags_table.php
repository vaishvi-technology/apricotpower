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
        Schema::create('item_tags', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Basic info (value = Lunar compatibility for ItemTagName)
            $table->string('value', 100)->nullable();
            $table->longText('description')->nullable();

            // Flags
            $table->boolean('is_stealth')->default(false);
            $table->boolean('is_hidden')->default(false);

            // Badge
            $table->string('badge_image', 100)->nullable();
            $table->longText('badge_description')->nullable();

            // SEO / Meta fields
            $table->string('meta_title', 255)->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();

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
        Schema::dropIfExists('item_tags');
    }
};
