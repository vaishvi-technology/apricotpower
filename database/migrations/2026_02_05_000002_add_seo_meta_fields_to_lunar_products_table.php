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
        Schema::table('products', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_og_title')->nullable();
            $table->string('meta_og_keywords')->nullable();
            $table->string('meta_og_image')->nullable();
            $table->string('meta_og_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_og_title',
                'meta_og_keywords',
                'meta_og_image',
                'meta_og_url',
            ]);
        });
    }
};
