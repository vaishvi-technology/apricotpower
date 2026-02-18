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
        Schema::create('items', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Lunar required fields for compatibility
            $table->foreignId('product_type_id')->constrained('lunar_product_types');
            $table->string('status')->index();
            $table->json('attribute_data')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained('lunar_brands');

            // Basic item info
            $table->string('title', 100)->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();

            // Pricing
            $table->double('price')->nullable();
            $table->double('cost')->nullable();
            $table->double('handling')->nullable();
            $table->double('discounted_price')->nullable();
            $table->integer('discount')->nullable();
            $table->boolean('is_discount_percentage')->nullable();
            $table->double('tally_price')->nullable();

            // Shipping
            $table->integer('shipping_weight_lb')->nullable();
            $table->integer('shipping_weight_oz')->nullable();
            $table->boolean('is_free_shipping')->default(false);
            $table->integer('shipping_restriction_id')->nullable();
            $table->longText('shipping_restrictions')->nullable();

            // Inventory
            $table->integer('quantity_available')->nullable();
            $table->integer('reorder_alert')->nullable();
            $table->boolean('track_inventory')->default(true);
            $table->double('daily_sales_avg')->nullable();
            $table->date('inventory_arrival_date')->nullable();
            $table->integer('lead_time')->nullable();
            $table->string('inventory_notes', 500)->nullable();

            // Category and organization
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->nullOnDelete();
            $table->integer('rank')->nullable();

            // Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_combo')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('has_options')->default(false);
            $table->boolean('is_checkout_featured')->default(false);
            $table->boolean('always_show_stock')->default(false);

            // Images
            $table->string('image_small', 50)->nullable();
            $table->string('image_large', 50)->nullable();

            // Identifiers and SKUs
            $table->string('sku', 255)->nullable();
            $table->string('upc', 255)->nullable();
            $table->string('amazon_sku', 20)->nullable();

            // Keywords and search
            $table->string('keywords', 255)->nullable();

            // Size and quantity
            $table->string('size_quantity', 150)->nullable();
            $table->integer('purchase_limit')->nullable();

            // Related items
            $table->integer('related_item_1_id')->nullable();
            $table->integer('related_item_2_id')->nullable();
            $table->integer('related_item_3_id')->nullable();

            // External integrations
            $table->integer('infusionsoft_id')->nullable();
            $table->bigInteger('shop_item_id')->nullable();
            $table->bigInteger('shop_variant_id')->nullable();
            $table->boolean('shop_skip_processing')->default(false);
            $table->integer('quickbooks_id')->nullable();

            // SEO / Meta fields
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 180)->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->string('og_title', 255)->nullable();
            $table->string('og_type', 50)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->string('og_url', 255)->nullable();

            // Supplier info
            $table->string('supplier_company', 100)->nullable();
            $table->string('supplier_contact_name', 100)->nullable();
            $table->string('supplier_phone', 100)->nullable();
            $table->string('supplier_email', 100)->nullable();
            $table->string('supplier_terms', 100)->nullable();

            // Additional fields
            $table->string('descriptor', 200)->nullable();
            $table->longText('resources')->nullable();
            $table->longText('disclaimer')->nullable();
            $table->boolean('requires_disclaimer_agreement')->default(false);
            $table->boolean('sb_send_as_combo')->default(false);

            // Feefo reviews
            $table->double('feefo_rating')->nullable();
            $table->integer('feefo_review_count')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
