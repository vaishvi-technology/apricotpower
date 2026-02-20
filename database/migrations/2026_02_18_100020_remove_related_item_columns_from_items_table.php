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
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'related_item_1_id',
                'related_item_2_id',
                'related_item_3_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('related_item_1_id')->nullable()->after('purchase_limit');
            $table->integer('related_item_2_id')->nullable()->after('related_item_1_id');
            $table->integer('related_item_3_id')->nullable()->after('related_item_2_id');
        });
    }
};
