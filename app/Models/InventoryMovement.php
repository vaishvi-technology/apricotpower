<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'inventory_lot_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'reason',
        'user_id',
    ];

    public const TYPE_RECEIVED = 'received';
    public const TYPE_SOLD = 'sold';
    public const TYPE_ADJUSTED = 'adjusted';
    public const TYPE_RETURNED = 'returned';
    public const TYPE_DAMAGED = 'damaged';
    public const TYPE_TRANSFERRED = 'transferred';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function inventoryLot(): BelongsTo
    {
        return $this->belongsTo(InventoryLot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getIsAdditionAttribute(): bool
    {
        return $this->quantity > 0;
    }

    public function getIsRemovalAttribute(): bool
    {
        return $this->quantity < 0;
    }
}
