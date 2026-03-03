<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingShipment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'supplier_id',
        'quantity',
        'expected_date',
        'tracking_url',
        'notes',
        'status',
        'inventory_lot_id',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    /**
     * Get the product for this shipment.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant for this shipment.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the supplier for this shipment.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the inventory lot created when this shipment was received.
     */
    public function inventoryLot(): BelongsTo
    {
        return $this->belongsTo(InventoryLot::class);
    }

    /**
     * Scope to only include pending shipments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to only include in-transit shipments.
     */
    public function scopeInTransit($query)
    {
        return $query->where('status', self::STATUS_IN_TRANSIT);
    }

    /**
     * Scope to only include overdue shipments.
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_TRANSIT])
            ->whereNotNull('expected_date')
            ->where('expected_date', '<', now());
    }

    /**
     * Scope to only include active (not received or cancelled) shipments.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_TRANSIT]);
    }

    /**
     * Check if the shipment is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->expected_date
            && $this->expected_date->isPast()
            && !in_array($this->status, [self::STATUS_RECEIVED, self::STATUS_CANCELLED]);
    }

    /**
     * Get status options for forms.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
