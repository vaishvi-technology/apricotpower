<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CustomerGroupPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'customer_group_id',
        'min_quantity',
        'price',
        'is_by_quantity',
        'cutoff_amount',
        'expires_at',
        'is_base_price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cutoff_amount' => 'decimal:2',
            'is_by_quantity' => 'boolean',
            'is_base_price' => 'boolean',
            'expires_at' => 'date',
        ];
    }

    /**
     * Scope to only include active (non-expired) prices.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', now()->startOfDay());
        });
    }

    /**
     * Scope to only include base group prices (not tier prices).
     */
    public function scopeBasePrices(Builder $query): Builder
    {
        return $query->where('is_base_price', true);
    }

    /**
     * Scope to only include tier prices (not base group prices).
     */
    public function scopeTierPrices(Builder $query): Builder
    {
        return $query->where('is_base_price', false);
    }

    /**
     * Scope to get prices for a specific customer group.
     */
    public function scopeForGroup(Builder $query, int $groupId): Builder
    {
        return $query->where('customer_group_id', $groupId);
    }

    /**
     * Scope to get prices for a specific product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Check if this price is expired.
     */
    public function isExpired(): bool
    {
        if (is_null($this->expires_at)) {
            return false;
        }

        return $this->expires_at->lt(now()->startOfDay());
    }

    /**
     * Get the product that owns this price.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant that owns this price.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the customer group that owns this price.
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
