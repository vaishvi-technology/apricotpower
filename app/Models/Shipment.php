<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'carrier',
        'service',
        'tracking_number',
        'tracking_url',
        'status',
        'weight',
        'shipping_cost',
        'label_url',
        'items',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_EXCEPTION = 'exception';

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'items' => 'array',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeShipped($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SHIPPED,
            self::STATUS_IN_TRANSIT,
        ]);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function getIsShippedAttribute(): bool
    {
        return in_array($this->status, [
            self::STATUS_SHIPPED,
            self::STATUS_IN_TRANSIT,
            self::STATUS_DELIVERED,
        ]);
    }

    public function getTrackingLinkAttribute(): ?string
    {
        if ($this->tracking_url) {
            return $this->tracking_url;
        }

        if (!$this->tracking_number || !$this->carrier) {
            return null;
        }

        return match (strtolower($this->carrier)) {
            'ups' => "https://www.ups.com/track?tracknum={$this->tracking_number}",
            'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$this->tracking_number}",
            'usps' => "https://tools.usps.com/go/TrackConfirmAction?tLabels={$this->tracking_number}",
            'dhl' => "https://www.dhl.com/en/express/tracking.html?AWB={$this->tracking_number}",
            default => null,
        };
    }
}
