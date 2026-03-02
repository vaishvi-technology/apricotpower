<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('iso3')->nullable()->change();
            $table->string('phonecode')->nullable()->change();
            $table->string('currency')->nullable()->change();
            $table->string('emoji')->nullable()->change();
            $table->string('emoji_u')->nullable()->change();
            $table->unsignedTinyInteger('rank')->default(0)->after('emoji_u');
            $table->boolean('group_at_top')->default(false)->after('rank');
            $table->boolean('hide')->default(false)->after('group_at_top');
            $table->unsignedSmallInteger('sb_zone')->nullable()->after('hide');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('iso3')->nullable(false)->change();
            $table->string('phonecode')->nullable(false)->change();
            $table->string('currency')->nullable(false)->change();
            $table->string('emoji')->nullable(false)->change();
            $table->string('emoji_u')->nullable(false)->change();
            $table->dropColumn(['rank', 'group_at_top', 'hide', 'sb_zone']);
        });
    }
};
