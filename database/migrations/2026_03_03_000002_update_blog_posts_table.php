<?php

use Illuminate\Database\Migrations\Migration;

// This migration is no longer needed — blog_posts table already includes
// is_nav_featured, is_pinned, and the correct author FK in the create migration.
return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
