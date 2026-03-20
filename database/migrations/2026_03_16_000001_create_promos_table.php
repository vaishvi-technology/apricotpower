<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('coupon_code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->string('landing_url')->nullable();
            $table->timestamp('valid_start')->nullable();
            $table->timestamp('valid_end')->nullable();
            $table->integer('limit_per_customer')->default(0);
            $table->integer('limit_total')->default(0);
            $table->string('account_groups')->nullable();
            $table->string('countries')->nullable();
            $table->text('autocart_items')->nullable();
            $table->boolean('disable_volume_discounts')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('coupon_code');
            $table->index('is_active');
            $table->index('is_auto');
            $table->index(['valid_start', 'valid_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
