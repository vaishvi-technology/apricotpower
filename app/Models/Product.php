<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Lunar\Models\Product as LunarProduct;

class Product extends LunarProduct
{
    /**
     * Product detail fields stored as direct database columns.
     * These override Lunar's attribute_data JSON storage for better performance and queryability.
     */
    protected const COLUMN_ATTRIBUTES = ['name', 'description'];

    protected $fillable = [
        // Original Lunar fields
        'attribute_data',
        'status',
        'brand_id',
        'category_id',
        // Product detail fields (direct columns instead of attribute_data JSON)
        'name',
        'description',
        // SEO meta fields
        'meta_title',
        'meta_keywords',
        'meta_description',
        'meta_og_title',
        'meta_og_keywords',
        'meta_og_image',
        'meta_og_url',
        // Content tabs
        'intro_content',
        'learn_more',
        // Product details
        'quantity_size',
        // Inventory notification settings
        'notify_at',
        'low_stock_notified_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'notify_at' => 'integer',
        'low_stock_notified_at' => 'datetime',
    ];

    /**
     * Override translateAttribute to read from direct database columns.
     * Falls back to parent's attribute_data JSON for other attributes.
     *
     * @param string $attribute
     * @param string|null $locale
     * @return mixed
     */
    public function translateAttribute(string $attribute, ?string $locale = null): mixed
    {
        // If this attribute is stored as a direct column, return it
        // (direct columns don't support translations, so locale is ignored)
        if (in_array($attribute, self::COLUMN_ATTRIBUTES)) {
            return $this->{$attribute};
        }

        // Fall back to Lunar's attribute_data JSON for other attributes
        return parent::translateAttribute($attribute, $locale);
    }

    /**
     * Get the single category (backwards compatibility).
     * @deprecated Use categories() relationship instead.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all categories for this product (many-to-many).
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function productBadges(): HasMany
    {
        return $this->hasMany(ProductBadge::class);
    }

    /**
     * Get badge keys as an array.
     */
    public function getBadgeKeysAttribute(): array
    {
        return $this->productBadges->pluck('badge_key')->toArray();
    }

    public function nutritionFact(): HasOne
    {
        return $this->hasOne(ProductNutritionFact::class);
    }

    /**
     * Override thumbnail relationship to get first image from collection.
     * Parent's thumbnail() queries for custom_properties->primary = true,
     * but our images may not have this flag set.
     */
    public function thumbnail(): MorphOne
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', 'images')
            ->orderBy('order_column');
    }

    /**
     * Get all inventory lots for this product.
     */
    public function inventoryLots(): HasMany
    {
        return $this->hasMany(InventoryLot::class);
    }

    /**
     * Get all inventory movements for this product.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get total stock (including expired) for this product.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->inventoryLots()->sum('quantity');
    }

    /**
     * Get available (non-expired, in-stock) quantity for this product.
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->inventoryLots()
            ->inStock()
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->sum('quantity');
    }

    /**
     * Check if product stock is at or below the notification threshold.
     */
    public function isLowStock(): bool
    {
        // notify_at of 0 means notifications are disabled
        if (!$this->notify_at || $this->notify_at <= 0) {
            return false;
        }

        return $this->available_stock <= $this->notify_at;
    }

    /**
     * Check if a low stock notification should be sent.
     * Returns true if stock is low and notification hasn't been sent recently (within 24 hours).
     */
    public function shouldSendLowStockNotification(): bool
    {
        if (!$this->isLowStock()) {
            return false;
        }

        // If notification was never sent, or sent more than 24 hours ago
        if (!$this->low_stock_notified_at) {
            return true;
        }

        return $this->low_stock_notified_at->lt(now()->subHours(24));
    }

    /**
     * Mark low stock notification as sent.
     */
    public function markLowStockNotified(): void
    {
        $this->update(['low_stock_notified_at' => now()]);
    }

    /**
     * Reset low stock notification when stock is replenished above threshold.
     */
    public function resetLowStockNotification(): void
    {
        if ($this->low_stock_notified_at && !$this->isLowStock()) {
            $this->update(['low_stock_notified_at' => null]);
        }
    }
}
