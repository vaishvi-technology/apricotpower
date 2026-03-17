<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable()->comment('Uploaded SVG/PNG logo');
            $table->string('share_url_pattern')->nullable()->comment('e.g. https://facebook.com/sharer/sharer.php?u={url}');
            $table->string('color', 20)->nullable()->comment('Brand hex color, e.g. #1877F2');
            $table->boolean('open_in_new_tab')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_post_social_link', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('social_link_id')->constrained('social_links')->cascadeOnDelete();
            $table->string('custom_url')->nullable()->comment('Override URL for this specific post');
            $table->primary(['blog_post_id', 'social_link_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_social_link');
        Schema::dropIfExists('social_links');
    }
};
