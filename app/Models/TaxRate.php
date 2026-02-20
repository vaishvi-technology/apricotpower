<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'state',
        'postal_code',
        'city',
        'rate',
        'is_compound',
        'is_active',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'is_compound' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLocation($query, string $country, ?string $state = null, ?string $city = null, ?string $postalCode = null)
    {
        return $query->active()
            ->where('country', $country)
            ->where(function ($q) use ($state) {
                $q->whereNull('state')
                    ->orWhere('state', $state);
            })
            ->where(function ($q) use ($city) {
                $q->whereNull('city')
                    ->orWhere('city', $city);
            })
            ->where(function ($q) use ($postalCode) {
                $q->whereNull('postal_code')
                    ->orWhere('postal_code', $postalCode);
            })
            ->orderByDesc('priority')
            ->orderByRaw('CASE WHEN postal_code IS NOT NULL THEN 1 ELSE 0 END DESC')
            ->orderByRaw('CASE WHEN city IS NOT NULL THEN 1 ELSE 0 END DESC')
            ->orderByRaw('CASE WHEN state IS NOT NULL THEN 1 ELSE 0 END DESC');
    }

    public function getRatePercentageAttribute(): float
    {
        return $this->rate * 100;
    }

    public function calculateTax(float $amount): float
    {
        return round($amount * $this->rate, 2);
    }

    public static function calculateTaxForLocation(float $amount, string $country, ?string $state = null, ?string $city = null, ?string $postalCode = null): array
    {
        $rates = static::forLocation($country, $state, $city, $postalCode)->get();

        if ($rates->isEmpty()) {
            return [
                'tax_amount' => 0,
                'rates' => [],
            ];
        }

        $totalTax = 0;
        $appliedRates = [];
        $taxableAmount = $amount;

        foreach ($rates as $rate) {
            $tax = $rate->calculateTax($taxableAmount);
            $totalTax += $tax;

            $appliedRates[] = [
                'name' => $rate->name,
                'rate' => $rate->rate_percentage,
                'amount' => $tax,
            ];

            if ($rate->is_compound) {
                $taxableAmount += $tax;
            }
        }

        return [
            'tax_amount' => $totalTax,
            'rates' => $appliedRates,
        ];
    }
}
