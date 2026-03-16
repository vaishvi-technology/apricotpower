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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->boolean('free_shipping')->default(false)->after('shippable');
            $table->decimal('weight_lbs', 10, 2)->default(0)->after('free_shipping');
            $table->decimal('weight_oz', 10, 2)->default(0)->after('weight_lbs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['free_shipping', 'weight_lbs', 'weight_oz']);
        });
    }
};
