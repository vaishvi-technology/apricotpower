<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'sku',
        'name',
        'variant_name',
        'options',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'total',
        'weight',
        'is_taxable',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'weight' => 'decimal:2',
            'is_taxable' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->variant_name) {
            return "{$this->name} - {$this->variant_name}";
        }

        return $this->name;
    }

    public function getTotalWeightAttribute(): float
    {
        return ($this->weight ?? 0) * $this->quantity;
    }
}
