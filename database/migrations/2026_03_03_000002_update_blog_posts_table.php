<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->boolean('is_nav_featured')->default(false)->after('is_featured');
            $table->boolean('is_pinned')->default(false)->after('is_nav_featured');

            // Drop the existing FK to users and re-add pointing to staff
            $table->dropForeign(['author_id']);
            $table->foreign('author_id')->references('id')->on('staff')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['is_nav_featured', 'is_pinned']);

            $table->dropForeign(['author_id']);
            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
