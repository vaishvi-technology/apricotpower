<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Integration logs for API sync tracking
        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('integration'); // shopify, quickbooks, shipstation, etc.
            $table->string('action'); // sync, push, pull, webhook
            $table->string('entity_type')->nullable(); // product, order, customer
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('status'); // pending, success, failed
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('integration');
            $table->index('status');
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });

        // External product ID mappings
        Schema::create('product_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('integration'); // shopify, amazon, etc.
            $table->string('external_id');
            $table->json('metadata')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'integration']);
            $table->index(['integration', 'external_id']);
        });

        // External order ID mappings
        Schema::create('order_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('integration'); // shopify, amazon, shipstation, etc.
            $table->string('external_id');
            $table->json('metadata')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'integration']);
            $table->index(['integration', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_integrations');
        Schema::dropIfExists('product_integrations');
        Schema::dropIfExists('integration_logs');
    }
};
