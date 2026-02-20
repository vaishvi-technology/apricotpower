<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function regions(): HasMany
    {
        return $this->hasMany(ShippingZoneRegion::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function activeRates(): HasMany
    {
        return $this->hasMany(ShippingRate::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function findForAddress(string $country, ?string $state = null, ?string $postalCode = null): ?self
    {
        return static::active()
            ->whereHas('regions', function ($query) use ($country, $state) {
                $query->where('country', $country)
                    ->where(function ($q) use ($state) {
                        $q->whereNull('state')
                            ->orWhere('state', $state);
                    });
            })
            ->orderBy('sort_order')
            ->first();
    }
}
