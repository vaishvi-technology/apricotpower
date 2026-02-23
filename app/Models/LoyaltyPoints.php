<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyPoints extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'balance',
        'lifetime_earned',
        'lifetime_redeemed',
        'tier',
    ];

    public const TIER_BRONZE = 'bronze';
    public const TIER_SILVER = 'silver';
    public const TIER_GOLD = 'gold';
    public const TIER_PLATINUM = 'platinum';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function earnedTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class)
            ->where('type', LoyaltyTransaction::TYPE_EARNED);
    }

    public function redeemedTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class)
            ->where('type', LoyaltyTransaction::TYPE_REDEEMED);
    }

    public function addPoints(int $points, ?LoyaltyRule $rule = null, ?Order $order = null, ?string $description = null): LoyaltyTransaction
    {
        $this->balance += $points;
        $this->lifetime_earned += $points;
        $this->save();

        $this->updateTier();

        return $this->transactions()->create([
            'loyalty_rule_id' => $rule?->id,
            'order_id' => $order?->id,
            'type' => LoyaltyTransaction::TYPE_EARNED,
            'points' => $points,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Points earned',
        ]);
    }

    public function redeemPoints(int $points, ?Order $order = null, ?string $description = null): ?LoyaltyTransaction
    {
        if ($points > $this->balance) {
            return null;
        }

        $this->balance -= $points;
        $this->lifetime_redeemed += $points;
        $this->save();

        return $this->transactions()->create([
            'order_id' => $order?->id,
            'type' => LoyaltyTransaction::TYPE_REDEEMED,
            'points' => -$points,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Points redeemed',
        ]);
    }

    public function adjustPoints(int $points, string $description): LoyaltyTransaction
    {
        $this->balance += $points;

        if ($points > 0) {
            $this->lifetime_earned += $points;
        }

        $this->save();
        $this->updateTier();

        return $this->transactions()->create([
            'type' => LoyaltyTransaction::TYPE_ADJUSTED,
            'points' => $points,
            'balance_after' => $this->balance,
            'description' => $description,
        ]);
    }

    public function canRedeem(int $points): bool
    {
        return $this->balance >= $points;
    }

    protected function updateTier(): void
    {
        $tier = match (true) {
            $this->lifetime_earned >= 10000 => self::TIER_PLATINUM,
            $this->lifetime_earned >= 5000 => self::TIER_GOLD,
            $this->lifetime_earned >= 1000 => self::TIER_SILVER,
            default => self::TIER_BRONZE,
        };

        if ($tier !== $this->tier) {
            $this->update(['tier' => $tier]);
        }
    }

    public static function getOrCreateForCustomer(Customer $customer): self
    {
        return static::firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'balance' => 0,
                'lifetime_earned' => 0,
                'lifetime_redeemed' => 0,
                'tier' => self::TIER_BRONZE,
            ]
        );
    }
}
