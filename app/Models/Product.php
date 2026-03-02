<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Lunar\Models\Product as LunarProduct;

class Product extends LunarProduct
{
    protected $fillable = [
        // Original Lunar fields
        'attribute_data',
        'status',
        'brand_id',
        'category_id',
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
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
