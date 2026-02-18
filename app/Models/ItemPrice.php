<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPrice extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'item_prices';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'item_id',
        'group_id',
        'is_by_quantity',
        'cutoff',
        'price',
        'ends_at',
        'is_expire_alert_sent',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_by_quantity' => 'boolean',
        'cutoff' => 'double',
        'price' => 'double',
        'ends_at' => 'date',
        'is_expire_alert_sent' => 'boolean',
    ];

    /**
     * Get the item this price belongs to.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Scope for active prices (not expired).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('ends_at')
              ->orWhere('ends_at', '>=', now());
        });
    }

    /**
     * Scope for expired prices.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('ends_at')
            ->where('ends_at', '<', now());
    }
}
