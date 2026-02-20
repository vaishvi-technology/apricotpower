<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolumeDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_group_id',
        'category_id',
        'min_quantity',
        'max_quantity',
        'discount_percentage',
        'discount_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForQuantity($query, int $quantity)
    {
        return $query->where('min_quantity', '<=', $quantity)
            ->where(function ($q) use ($quantity) {
                $q->whereNull('max_quantity')
                    ->orWhere('max_quantity', '>=', $quantity);
            });
    }

    public function calculateDiscount(float $unitPrice): float
    {
        if ($this->discount_percentage) {
            return $unitPrice * ($this->discount_percentage / 100);
        }

        if ($this->discount_amount) {
            return min($this->discount_amount, $unitPrice);
        }

        return 0;
    }
}
