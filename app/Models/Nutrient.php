<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nutrient extends Model
{
    protected $fillable = [
        'legacy_id',
        'name',
        'description',
    ];

    public function productNutrients(): HasMany
    {
        return $this->hasMany(ProductNutrient::class);
    }
}
