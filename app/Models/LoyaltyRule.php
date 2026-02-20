<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'action',
        'points_per_dollar',
        'fixed_points',
        'multiplier',
        'redemption_value',
        'min_points_to_redeem',
        'max_points_per_order',
        'customer_group_id',
        'is_active',
        'starts_at',
        'expires_at',
        'priority',
    ];

    public const TYPE_EARN = 'earn';
    public const TYPE_REDEEM = 'redeem';
    public const TYPE_BONUS = 'bonus';

    public const ACTION_ORDER_PLACED = 'order_placed';
    public const ACTION_SIGNUP = 'signup';
    public const ACTION_REVIEW = 'review';
    public const ACTION_BIRTHDAY = 'birthday';
    public const ACTION_REFERRAL = 'referral';

    protected function casts(): array
    {
        return [
            'points_per_dollar' => 'decimal:2',
            'multiplier' => 'decimal:2',
            'redemption_value' => 'decimal:4',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
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
            });
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeEarningRules($query)
    {
        return $query->where('type', self::TYPE_EARN);
    }

    public function scopeRedemptionRules($query)
    {
        return $query->where('type', self::TYPE_REDEEM);
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

        return true;
    }

    public function calculatePoints(float $orderTotal): int
    {
        if (!$this->is_valid || $this->type !== self::TYPE_EARN) {
            return 0;
        }

        $points = 0;

        if ($this->points_per_dollar) {
            $points = (int) floor($orderTotal * $this->points_per_dollar);
        }

        if ($this->fixed_points) {
            $points = $this->fixed_points;
        }

        $points = (int) floor($points * $this->multiplier);

        if ($this->max_points_per_order) {
            $points = min($points, $this->max_points_per_order);
        }

        return $points;
    }

    public function calculateRedemptionValue(int $points): float
    {
        if (!$this->is_valid || $this->type !== self::TYPE_REDEEM || !$this->redemption_value) {
            return 0;
        }

        return round($points * $this->redemption_value, 2);
    }

    public function appliesToCustomer(?Customer $customer): bool
    {
        if (!$customer) {
            return $this->customer_group_id === null;
        }

        if ($this->customer_group_id === null) {
            return true;
        }

        return $customer->customer_group_id === $this->customer_group_id;
    }
}
