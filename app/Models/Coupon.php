<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit',
        'usage_limit_per_customer',
        'times_used',
        'applies_to_all_products',
        'exclude_sale_items',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED_AMOUNT = 'fixed_amount';
    public const TYPE_FREE_SHIPPING = 'free_shipping';

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'maximum_discount_amount' => 'decimal:2',
            'applies_to_all_products' => 'boolean',
            'exclude_sale_items' => 'boolean',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('times_used', '<', 'usage_limit');
            });
    }

    public function getIsValidAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeUsedByCustomer(?Customer $customer): bool
    {
        if (!$this->is_valid) {
            return false;
        }

        if (!$customer || !$this->usage_limit_per_customer) {
            return true;
        }

        $customerUsageCount = $this->usages()
            ->where('customer_id', $customer->id)
            ->count();

        return $customerUsageCount < $this->usage_limit_per_customer;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->minimum_order_amount && $subtotal < $this->minimum_order_amount) {
            return 0;
        }

        $discount = match ($this->discount_type) {
            self::TYPE_PERCENTAGE => $subtotal * ($this->discount_value / 100),
            self::TYPE_FIXED_AMOUNT => (float) $this->discount_value,
            self::TYPE_FREE_SHIPPING => 0,
            default => 0,
        };

        if ($this->maximum_discount_amount) {
            $discount = min($discount, $this->maximum_discount_amount);
        }

        return min($discount, $subtotal);
    }
}
