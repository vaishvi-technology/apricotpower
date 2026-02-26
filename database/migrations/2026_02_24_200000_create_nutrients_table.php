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
        Schema::create('nutrients', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('legacy_id')->nullable()->index();
            $table->string('name');
            $table->string('display_title');
            $table->string('display_class')->nullable();
            $table->integer('rank')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_funky')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrients');
    }
};
