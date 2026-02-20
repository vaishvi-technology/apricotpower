<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'business_name',
        'slug',
        'description',
        'website',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'hours_of_operation',
        'logo',
        'is_featured',
        'show_on_locator',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'hours_of_operation' => 'array',
            'is_featured' => 'boolean',
            'show_on_locator' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFormattedAddressAttribute(): string
    {
        $lines = [
            $this->address_line_1,
            $this->address_line_2,
            "{$this->city}, {$this->state} {$this->postal_code}",
            $this->country,
        ];

        return implode("\n", array_filter($lines));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnLocator($query)
    {
        return $query->where('show_on_locator', true);
    }

    public function scopeNearby($query, float $latitude, float $longitude, int $radiusMiles = 50)
    {
        $radiusKm = $radiusMiles * 1.60934;

        return $query->selectRaw("
            *, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }
}
