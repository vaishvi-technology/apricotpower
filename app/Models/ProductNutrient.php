<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductNutrient extends Model
{
    protected $fillable = [
        'nutrition_fact_id',
        'nutrient_id',
        'amount_per_serving',
        'percent_daily_value',
        'not_established',
    ];

    protected $casts = [
        'percent_daily_value' => 'decimal:2',
        'not_established' => 'boolean',
    ];

    public function nutritionFact(): BelongsTo
    {
        return $this->belongsTo(ProductNutritionFact::class, 'nutrition_fact_id');
    }

    public function nutrient(): BelongsTo
    {
        return $this->belongsTo(Nutrient::class);
    }

    public function getDisplayValueAttribute(): string
    {
        if ($this->not_established) {
            return '†';
        }

        if ($this->percent_daily_value !== null) {
            return $this->percent_daily_value . '%';
        }

        return '';
    }
}
