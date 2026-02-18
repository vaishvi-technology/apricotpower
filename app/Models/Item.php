<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Lunar\Base\Casts\AsAttributeData;
use Lunar\Models\Product as LunarProduct;

class Item extends LunarProduct
{
    /**
     * The table associated with the model.
     */
    protected $table = 'items';

    /**
     * Override getTable to prevent Lunar's automatic prefix.
     */
    public function getTable(): string
    {
        return 'items';
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Lunar required fields
        'product_type_id',
        'status',
        'attribute_data',
        'brand_id',

        // Basic item info
        'title',
        'short_description',
        'description',

        // Pricing
        'price',
        'cost',
        'handling',
        'discounted_price',
        'discount',
        'is_discount_percentage',
        'tally_price',

        // Shipping
        'shipping_weight_lb',
        'shipping_weight_oz',
        'is_free_shipping',
        'shipping_restriction_id',
        'shipping_restrictions',

        // Inventory
        'quantity_available',
        'reorder_alert',
        'track_inventory',
        'daily_sales_avg',
        'inventory_arrival_date',
        'lead_time',
        'inventory_notes',

        // Category and organization
        'category_id',
        'rank',

        // Flags
        'is_featured',
        'is_hidden',
        'is_taxable',
        'is_combo',
        'is_new',
        'has_options',
        'is_checkout_featured',
        'always_show_stock',

        // Images
        'image_small',
        'image_large',

        // Identifiers and SKUs
        'sku',
        'upc',
        'amazon_sku',

        // Keywords and search
        'keywords',

        // Size and quantity
        'size_quantity',
        'purchase_limit',

        // Related items
        'related_item_1_id',
        'related_item_2_id',
        'related_item_3_id',

        // External integrations
        'infusionsoft_id',
        'shop_item_id',
        'shop_variant_id',
        'shop_skip_processing',
        'quickbooks_id',

        // SEO / Meta fields
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_type',
        'og_image',
        'og_url',

        // Supplier info
        'supplier_company',
        'supplier_contact_name',
        'supplier_phone',
        'supplier_email',
        'supplier_terms',

        // Additional fields
        'descriptor',
        'resources',
        'disclaimer',
        'requires_disclaimer_agreement',
        'sb_send_as_combo',

        // Feefo reviews
        'feefo_rating',
        'feefo_review_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class,
        'is_featured' => 'boolean',
        'is_hidden' => 'boolean',
        'is_free_shipping' => 'boolean',
        'is_taxable' => 'boolean',
        'is_combo' => 'boolean',
        'is_new' => 'boolean',
        'has_options' => 'boolean',
        'is_checkout_featured' => 'boolean',
        'is_discount_percentage' => 'boolean',
        'track_inventory' => 'boolean',
        'shop_skip_processing' => 'boolean',
        'requires_disclaimer_agreement' => 'boolean',
        'always_show_stock' => 'boolean',
        'sb_send_as_combo' => 'boolean',
        'inventory_arrival_date' => 'date',
        'price' => 'double',
        'cost' => 'double',
        'handling' => 'double',
        'discounted_price' => 'double',
        'tally_price' => 'double',
        'daily_sales_avg' => 'double',
        'feefo_rating' => 'double',
    ];

    /**
     * Get related items.
     */
    public function relatedItem1()
    {
        return $this->belongsTo(self::class, 'related_item_1_id');
    }

    public function relatedItem2()
    {
        return $this->belongsTo(self::class, 'related_item_2_id');
    }

    public function relatedItem3()
    {
        return $this->belongsTo(self::class, 'related_item_3_id');
    }

    /**
     * Override Lunar's tags() to use item_tag_links pivot table.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            ItemTag::class,
            'taggable',
            'item_tag_links',
            'taggable_id',
            'tag_id'
        )->withTimestamps();
    }
}
