<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id',
        'name',
        'carrier',
        'service_code',
        'calculation_type',
        'base_rate',
        'per_unit_rate',
        'min_order_amount',
        'min_weight',
        'max_weight',
        'estimated_days_min',
        'estimated_days_max',
        'is_active',
        'sort_order',
    ];

    public const TYPE_FLAT_RATE = 'flat_rate';
    public const TYPE_WEIGHT_BASED = 'weight_based';
    public const TYPE_PRICE_BASED = 'price_based';
    public const TYPE_FREE = 'free';

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'per_unit_rate' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'min_weight' => 'decimal:2',
            'max_weight' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateRate(float $orderTotal, float $totalWeight): float
    {
        if ($this->calculation_type === self::TYPE_FREE) {
            if ($this->min_order_amount && $orderTotal < $this->min_order_amount) {
                return (float) $this->base_rate;
            }
            return 0;
        }

        if ($this->calculation_type === self::TYPE_FLAT_RATE) {
            return (float) $this->base_rate;
        }

        if ($this->calculation_type === self::TYPE_WEIGHT_BASED) {
            return (float) $this->base_rate + ($totalWeight * $this->per_unit_rate);
        }

        if ($this->calculation_type === self::TYPE_PRICE_BASED) {
            return (float) $this->base_rate + ($orderTotal * $this->per_unit_rate);
        }

        return (float) $this->base_rate;
    }

    public function isAvailableForWeight(float $weight): bool
    {
        if ($this->min_weight && $weight < $this->min_weight) {
            return false;
        }

        if ($this->max_weight && $weight > $this->max_weight) {
            return false;
        }

        return true;
    }

    public function getEstimatedDeliveryAttribute(): ?string
    {
        if (!$this->estimated_days_min || !$this->estimated_days_max) {
            return null;
        }

        if ($this->estimated_days_min === $this->estimated_days_max) {
            return "{$this->estimated_days_min} business days";
        }

        return "{$this->estimated_days_min}-{$this->estimated_days_max} business days";
    }
}
