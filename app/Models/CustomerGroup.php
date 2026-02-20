<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_percentage',
        'is_wholesale',
        'net_terms_eligible',
        'net_terms_days',
        'minimum_order_amount',
        'requires_approval',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
            'is_wholesale' => 'boolean',
            'net_terms_eligible' => 'boolean',
            'minimum_order_amount' => 'decimal:2',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CustomerGroupPrice::class);
    }

    public function volumeDiscounts(): HasMany
    {
        return $this->hasMany(VolumeDiscount::class);
    }

    public function loyaltyRules(): HasMany
    {
        return $this->hasMany(LoyaltyRule::class);
    }
}
