<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nutrient extends Model
{
    protected $fillable = [
        'legacy_id',
        'name',
        'display_title',
        'display_class',
        'rank',
        'description',
        'is_funky',
        'is_active',
    ];

    protected $casts = [
        'is_funky' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function productNutrients(): HasMany
    {
        return $this->hasMany(ProductNutrient::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('rank');
    }
}
