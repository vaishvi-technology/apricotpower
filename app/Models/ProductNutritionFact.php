<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductNutritionFact extends Model
{
    protected $fillable = [
        'product_id',
        'serving_size',
        'servings_per_container',
        'calories_per_serving',
        'calories_from_fat',
        'is_enabled',
        'ingredients',
        'ingredients_enabled',
        'label_type',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'ingredients_enabled' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Lunar\Models\Product::class);
    }

    public function productNutrients(): HasMany
    {
        return $this->hasMany(ProductNutrient::class, 'nutrition_fact_id');
    }

    public function nutrients(): HasMany
    {
        return $this->hasMany(ProductNutrient::class, 'nutrition_fact_id');
    }

    public function getOrderedNutrients()
    {
        return $this->productNutrients()
            ->with('nutrient')
            ->get()
            ->sortBy(fn($pn) => $pn->nutrient->rank ?? 0);
    }
}
