<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'payment_method_id',
        'status',
        'frequency',
        'frequency_interval',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'shipping_phone',
        'next_order_date',
        'last_order_date',
        'orders_count',
        'discount_percentage',
        'notes',
        'cancelled_at',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    public const FREQUENCY_WEEKLY = 'weekly';
    public const FREQUENCY_BIWEEKLY = 'biweekly';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_QUARTERLY = 'quarterly';
    public const FREQUENCY_ANNUALLY = 'annually';

    protected function casts(): array
    {
        return [
            'next_order_date' => 'date',
            'last_order_date' => 'date',
            'discount_percentage' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeDueForRenewal($query)
    {
        return $query->active()
            ->where('next_order_date', '<=', now());
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function getShippingFullNameAttribute(): string
    {
        return "{$this->shipping_first_name} {$this->shipping_last_name}";
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }

    public function getDiscountAmountAttribute(): float
    {
        if (!$this->discount_percentage) {
            return 0;
        }

        return $this->subtotal * ($this->discount_percentage / 100);
    }

    public function getTotalAttribute(): float
    {
        return $this->subtotal - $this->discount_amount;
    }

    public function calculateNextOrderDate(): \Carbon\Carbon
    {
        $baseDate = $this->last_order_date ?? now();

        return match ($this->frequency) {
            self::FREQUENCY_WEEKLY => $baseDate->addWeeks($this->frequency_interval),
            self::FREQUENCY_BIWEEKLY => $baseDate->addWeeks(2 * $this->frequency_interval),
            self::FREQUENCY_MONTHLY => $baseDate->addMonths($this->frequency_interval),
            self::FREQUENCY_QUARTERLY => $baseDate->addMonths(3 * $this->frequency_interval),
            self::FREQUENCY_ANNUALLY => $baseDate->addYears($this->frequency_interval),
            default => $baseDate->addMonths($this->frequency_interval),
        };
    }

    public function pause(): void
    {
        $this->update(['status' => self::STATUS_PAUSED]);
    }

    public function resume(): void
    {
        if ($this->next_order_date->isPast()) {
            $this->update([
                'status' => self::STATUS_ACTIVE,
                'next_order_date' => $this->calculateNextOrderDate(),
            ]);
        } else {
            $this->update(['status' => self::STATUS_ACTIVE]);
        }
    }

    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }
}
