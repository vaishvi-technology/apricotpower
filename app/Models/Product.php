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
}
