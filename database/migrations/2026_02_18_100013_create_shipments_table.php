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
        Schema::create('shipments', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Reference to Lunar orders (no FK constraint - external table)
            $table->unsignedBigInteger('order_id')->nullable();

            // Shipment details
            $table->string('title', 100)->nullable();
            $table->datetime('ship_date')->nullable();
            $table->integer('status')->default(0); // 0=pending, 1=shipped, 2=delivered

            // Shipping method
            $table->string('shipping_type', 75)->nullable();
            $table->string('shipped_method', 100)->nullable();

            // Tracking
            $table->longText('tracking_number')->nullable();

            // Weight
            $table->integer('weight_lbs')->default(0);
            $table->integer('weight_oz')->default(0);

            // Carrier/Delivery tracking
            $table->string('carrier_status', 50)->nullable();
            $table->datetime('carrier_delivered_date')->nullable();
            $table->integer('delivered_status')->default(0);
            $table->date('delivered_date')->nullable();
            $table->date('delivered_checked')->nullable();

            // Subscribers for notifications
            $table->longText('subscribers')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('order_id');
            $table->index('status');
            $table->index('delivered_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
