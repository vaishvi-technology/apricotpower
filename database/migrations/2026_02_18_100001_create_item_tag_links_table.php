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
        Schema::create('item_tag_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('item_tags')->onDelete('cascade');
            $table->morphs('taggable'); // taggable_id, taggable_type for Lunar compatibility
            $table->timestamps();

            $table->unique(['tag_id', 'taggable_id', 'taggable_type'], 'item_tag_links_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_tag_links');
    }
};
