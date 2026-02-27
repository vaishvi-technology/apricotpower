<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Models\Product;

class ProductBadge extends Model
{
    protected $fillable = [
        'product_id',
        'badge_key',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get badge details from config.
     */
    public function getBadgeAttribute(): ?array
    {
        return config("badges.{$this->badge_key}");
    }
}
