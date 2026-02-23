<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'options' => 'array',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
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
        return ($this->unit_price * $this->quantity) - $this->discount_amount;
    }

    public function getNameAttribute(): string
    {
        $name = $this->product->name;

        if ($this->variant) {
            $name .= ' - ' . $this->variant->display_name;
        }

        return $name;
    }

    public function getSkuAttribute(): string
    {
        return $this->variant?->sku ?? $this->product->sku;
    }

    public function getWeightAttribute(): float
    {
        $weight = $this->variant?->weight ?? $this->product->weight ?? 0;
        return $weight * $this->quantity;
    }

    public function getIsAvailableAttribute(): bool
    {
        if (!$this->product->is_active) {
            return false;
        }

        if ($this->variant && !$this->variant->is_active) {
            return false;
        }

        if ($this->variant) {
            return $this->variant->is_in_stock || $this->product->allow_backorder;
        }

        return $this->product->is_in_stock;
    }
}
