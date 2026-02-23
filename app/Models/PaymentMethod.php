<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'provider',
        'provider_payment_method_id',
        'last_four',
        'brand',
        'exp_month',
        'exp_year',
        'billing_name',
        'billing_address_line_1',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'is_default',
        'is_active',
    ];

    public const TYPE_CARD = 'card';
    public const TYPE_BANK_ACCOUNT = 'bank_account';
    public const TYPE_NET_TERMS = 'net_terms';

    protected $hidden = [
        'provider_payment_method_id',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCards($query)
    {
        return $query->where('type', self::TYPE_CARD);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === self::TYPE_CARD) {
            return ucfirst($this->brand ?? 'Card') . ' ending in ' . $this->last_four;
        }

        if ($this->type === self::TYPE_BANK_ACCOUNT) {
            return 'Bank account ending in ' . $this->last_four;
        }

        return 'Net Terms';
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->type !== self::TYPE_CARD || !$this->exp_year || !$this->exp_month) {
            return false;
        }

        $expiry = \Carbon\Carbon::createFromDate($this->exp_year, $this->exp_month, 1)->endOfMonth();
        return $expiry->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if ($this->type !== self::TYPE_CARD || !$this->exp_year || !$this->exp_month) {
            return false;
        }

        $expiry = \Carbon\Carbon::createFromDate($this->exp_year, $this->exp_month, 1)->endOfMonth();
        return $expiry->isFuture() && $expiry->diffInDays(now()) <= 30;
    }
}
