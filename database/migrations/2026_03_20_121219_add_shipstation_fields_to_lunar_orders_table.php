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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipstation_order_id')->nullable()->index()->after('reference');
            $table->string('shipstation_carrier_code')->nullable()->after('shipstation_order_id');
            $table->string('shipstation_service_code')->nullable()->after('shipstation_carrier_code');
            $table->string('tracking_number')->nullable()->index()->after('shipstation_service_code');
            $table->string('shipping_status')->default('pending')->after('tracking_number');
            $table->timestamp('shipped_at')->nullable()->after('shipping_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipstation_order_id',
                'shipstation_carrier_code',
                'shipstation_service_code',
                'tracking_number',
                'shipping_status',
                'shipped_at',
            ]);
        });
    }
};
