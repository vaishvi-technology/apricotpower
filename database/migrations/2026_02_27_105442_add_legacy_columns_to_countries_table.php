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
        Schema::table('countries', function (Blueprint $table) {
            $table->unsignedTinyInteger('rank')->default(0)->after('emoji_u');
            $table->boolean('group_at_top')->default(false)->after('rank');
            $table->boolean('hide')->default(false)->after('group_at_top');
            $table->unsignedSmallInteger('sb_zone')->nullable()->after('hide');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['rank', 'group_at_top', 'hide', 'sb_zone']);
        });
    }
};
