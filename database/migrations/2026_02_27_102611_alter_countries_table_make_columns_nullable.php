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
        });
    }
};
