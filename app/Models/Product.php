<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Models\Product as LunarProduct;

class Product extends LunarProduct
{
    protected $fillable = [
        // Original Lunar fields
        'attribute_data',
        'product_type_id',
        'status',
        'brand_id',
        // SEO meta fields
        'meta_title',
        'meta_keywords',
        'meta_description',
        'meta_og_title',
        'meta_og_keywords',
        'meta_og_image',
        'meta_og_url',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'product_category'
        );
    }
}
