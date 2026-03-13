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
        Schema::table('nutrients', function (Blueprint $table) {
            $table->dropColumn(['display_title', 'display_class', 'rank', 'is_funky', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrients', function (Blueprint $table) {
            $table->string('display_title')->after('name');
            $table->string('display_class')->nullable()->after('display_title');
            $table->integer('rank')->default(0)->after('display_class');
            $table->boolean('is_funky')->default(false)->after('description');
            $table->boolean('is_active')->default(true)->after('is_funky');
        });
    }
};
