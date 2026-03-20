<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('promo_id')->nullable()->after('currency_id');
            $table->string('promo_code')->nullable()->after('promo_id');
            $table->decimal('promo_discount', 10, 2)->default(0)->after('promo_code');
            $table->boolean('promo_free_shipping')->default(false)->after('promo_discount');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['promo_id', 'promo_code', 'promo_discount', 'promo_free_shipping']);
        });
    }
};
