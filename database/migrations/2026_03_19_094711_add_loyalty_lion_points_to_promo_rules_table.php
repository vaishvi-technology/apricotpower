<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_rules', function (Blueprint $table) {
            $table->boolean('act_is_ll_points')->default(false)->after('act_bogo_limit');
            $table->integer('act_ll_points_amount')->default(0)->after('act_is_ll_points');
        });
    }

    public function down(): void
    {
        Schema::table('promo_rules', function (Blueprint $table) {
            $table->dropColumn(['act_is_ll_points', 'act_ll_points_amount']);
        });
    }
};
