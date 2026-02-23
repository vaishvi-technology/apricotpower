<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->integer('balance')->default(0);
            $table->integer('lifetime_earned')->default(0);
            $table->integer('lifetime_redeemed')->default(0);
            $table->string('tier')->default('bronze'); // bronze, silver, gold, platinum
            $table->timestamps();

            $table->unique('customer_id');
            $table->index('tier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
