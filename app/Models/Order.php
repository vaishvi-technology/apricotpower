<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'subscription_id',
        'status',
        'payment_status',
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
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'billing_phone',
        'billing_email',
        'subtotal',
        'discount_amount',
        'shipping_amount',
        'tax_amount',
        'total',
        'coupon_code',
        'shipping_method',
        'customer_notes',
        'admin_notes',
        'ip_address',
        'user_agent',
        'payment_due_date',
        'paid_at',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';
    public const PAYMENT_STATUS_NET_TERMS = 'net_terms';

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'payment_due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('created_at');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function couponUsage(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(OrderIntegration::class);
    }

    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('payment_status', [
            self::PAYMENT_STATUS_PENDING,
            self::PAYMENT_STATUS_NET_TERMS,
        ]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_NET_TERMS)
            ->whereNotNull('payment_due_date')
            ->where('payment_due_date', '<', now());
    }

    public function getShippingFullNameAttribute(): string
    {
        return "{$this->shipping_first_name} {$this->shipping_last_name}";
    }

    public function getBillingFullNameAttribute(): string
    {
        return "{$this->billing_first_name} {$this->billing_last_name}";
    }

    public function getFormattedShippingAddressAttribute(): string
    {
        $lines = [
            $this->shipping_full_name,
            $this->shipping_company,
            $this->shipping_address_line_1,
            $this->shipping_address_line_2,
            "{$this->shipping_city}, {$this->shipping_state} {$this->shipping_postal_code}",
            $this->shipping_country,
        ];

        return implode("\n", array_filter($lines));
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getTotalWeightAttribute(): float
    {
        return $this->items->sum('weight');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_NET_TERMS
            && $this->payment_due_date
            && $this->payment_due_date->isPast();
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function getIsCancellableAttribute(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
        ]);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = date('Ymd');
        $lastOrder = static::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('order_number')
            ->first();

        if ($lastOrder) {
            $sequence = (int) substr($lastOrder->order_number, -4) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
