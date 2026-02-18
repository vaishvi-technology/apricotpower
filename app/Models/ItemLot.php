<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemLot extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'item_lots';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'item_id',
        'lot_number',
        'expires_at',
        'quantity',
        'is_expired_notified',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expires_at' => 'date',
        'quantity' => 'integer',
        'is_expired_notified' => 'boolean',
    ];

    /**
     * Get the item this lot belongs to.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Scope for expired lots.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope for lots expiring soon.
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }
}
