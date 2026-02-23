<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingZoneRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id',
        'country',
        'state',
        'postal_code_pattern',
    ];

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function matchesAddress(string $country, ?string $state = null, ?string $postalCode = null): bool
    {
        if ($this->country !== $country) {
            return false;
        }

        if ($this->state && $this->state !== $state) {
            return false;
        }

        if ($this->postal_code_pattern && $postalCode) {
            if (!preg_match('/' . $this->postal_code_pattern . '/', $postalCode)) {
                return false;
            }
        }

        return true;
    }
}
