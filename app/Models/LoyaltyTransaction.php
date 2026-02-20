<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_points_id',
        'loyalty_rule_id',
        'order_id',
        'type',
        'points',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'expires_at',
    ];

    public const TYPE_EARNED = 'earned';
    public const TYPE_REDEEMED = 'redeemed';
    public const TYPE_EXPIRED = 'expired';
    public const TYPE_ADJUSTED = 'adjusted';
    public const TYPE_BONUS = 'bonus';

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function loyaltyPoints(): BelongsTo
    {
        return $this->belongsTo(LoyaltyPoints::class);
    }

    public function loyaltyRule(): BelongsTo
    {
        return $this->belongsTo(LoyaltyRule::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeEarned($query)
    {
        return $query->where('type', self::TYPE_EARNED);
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', self::TYPE_REDEEMED);
    }

    public function scopeExpired($query)
    {
        return $query->where('type', self::TYPE_EXPIRED);
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now())
            ->where('points', '>', 0);
    }

    public function getIsEarnedAttribute(): bool
    {
        return $this->type === self::TYPE_EARNED;
    }

    public function getIsRedeemedAttribute(): bool
    {
        return $this->type === self::TYPE_REDEEMED;
    }

    public function getAbsolutePointsAttribute(): int
    {
        return abs($this->points);
    }

    public function getFormattedPointsAttribute(): string
    {
        $prefix = $this->points > 0 ? '+' : '';
        return $prefix . number_format($this->points);
    }
}
