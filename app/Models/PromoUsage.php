<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_id',
        'customer_id',
        'customer_email',
        'order_id',
        'discount_amount',
        'free_shipping',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'free_shipping' => 'boolean',
        ];
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }
}
