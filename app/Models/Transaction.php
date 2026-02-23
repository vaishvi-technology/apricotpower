<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'payment_method_id',
        'type',
        'status',
        'amount',
        'currency',
        'provider',
        'provider_transaction_id',
        'provider_response_code',
        'provider_response_message',
        'provider_metadata',
        'last_four',
        'card_brand',
        'notes',
        'user_id',
    ];

    public const TYPE_CHARGE = 'charge';
    public const TYPE_REFUND = 'refund';
    public const TYPE_VOID = 'void';
    public const TYPE_CAPTURE = 'capture';
    public const TYPE_NET_TERMS_PAYMENT = 'net_terms_payment';

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $hidden = [
        'provider_transaction_id',
        'provider_metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'provider_metadata' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCharges($query)
    {
        return $query->where('type', self::TYPE_CHARGE);
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', self::TYPE_REFUND);
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getIsRefundAttribute(): bool
    {
        return $this->type === self::TYPE_REFUND;
    }

    public function getDisplayAmountAttribute(): string
    {
        $prefix = $this->is_refund ? '-' : '';
        return $prefix . '$' . number_format($this->amount, 2);
    }
}
