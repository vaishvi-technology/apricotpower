<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'coupon_code',
        'description',
        'is_active',
        'is_auto',
        'is_hidden',
        'landing_url',
        'valid_start',
        'valid_end',
        'limit_per_customer',
        'limit_total',
        'account_groups',
        'countries',
        'autocart_items',
        'disable_volume_discounts',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_auto' => 'boolean',
            'is_hidden' => 'boolean',
            'disable_volume_discounts' => 'boolean',
            'valid_start' => 'datetime',
            'valid_end' => 'datetime',
            'limit_per_customer' => 'integer',
            'limit_total' => 'integer',
        ];
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PromoRule::class)->orderBy('sort_order');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoUsage::class);
    }

    /**
     * Scope: only active, non-hidden promos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_hidden', false);
    }

    /**
     * Scope: currently valid (active + within date range).
     */
    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('valid_start')->orWhere('valid_start', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_end')->orWhere('valid_end', '>', now());
            });
    }

    /**
     * Scope: auto-apply promos.
     */
    public function scopeAuto($query)
    {
        return $query->where('is_auto', true);
    }

    /**
     * Check if this promo is currently valid (date + active).
     */
    public function getIsValidAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_start && $this->valid_start->isFuture()) {
            return false;
        }

        if ($this->valid_end && $this->valid_end->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Get status label.
     */
    public function getActiveCodeAttribute(): string
    {
        if (!$this->is_active) {
            return 'INACTIVE';
        }

        if ($this->valid_start && $this->valid_start->isFuture()) {
            return 'PENDING';
        }

        if ($this->valid_end && $this->valid_end->isPast()) {
            return 'EXPIRED';
        }

        if ($this->valid_end) {
            return 'ACTIVE_UNTIL';
        }

        return 'ACTIVE';
    }

    /**
     * Count total uses of this promo.
     */
    public function getUsedCountAttribute(): int
    {
        return $this->usages()->count();
    }

    /**
     * Check if total usage limit has been reached.
     */
    public function hasReachedTotalLimit(): bool
    {
        if ($this->limit_total <= 0) {
            return false;
        }

        return $this->used_count >= $this->limit_total;
    }

    /**
     * Check if per-customer usage limit has been reached.
     */
    public function hasReachedCustomerLimit(?int $customerId, ?string $customerEmail = null): bool
    {
        if ($this->limit_per_customer <= 0) {
            return false;
        }

        $query = $this->usages();

        if ($customerId) {
            $query->where('customer_id', $customerId);
        } elseif ($customerEmail) {
            $query->where('customer_email', $customerEmail);
        } else {
            return false;
        }

        return $query->count() >= $this->limit_per_customer;
    }

    /**
     * Get allowed account groups as array.
     */
    public function getAllowedAccountGroupsAttribute(): array
    {
        if (empty($this->account_groups)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $this->account_groups)));
    }

    /**
     * Get allowed countries as array.
     */
    public function getAllowedCountriesAttribute(): array
    {
        if (empty($this->countries)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $this->countries)));
    }

    /**
     * Parse autocart items string into array of [item_id => quantity].
     */
    public function getAutocartItemsParsedAttribute(): array
    {
        if (empty($this->autocart_items)) {
            return [];
        }

        $items = [];
        $parts = array_filter(explode('|', $this->autocart_items));

        foreach ($parts as $part) {
            $pair = explode('=', trim($part));
            if (count($pair) === 2) {
                $items[(int) $pair[0]] = (int) $pair[1];
            }
        }

        return $items;
    }
}
