<?php

namespace App\Shipping\DTOs;

class ShippingRateRequest
{
    public function __construct(
        public readonly string $toCountry,
        public readonly string $toPostalCode,
        public readonly string $toCity,
        public readonly ?string $toState = null,
        public readonly float $weight = 0,
        public readonly string $weightUnits = 'ounces',
        public readonly ?string $carrierCode = null,
        public readonly bool $residential = true,
        public readonly ?string $fromPostalCode = null,
        public readonly ?string $fromCity = null,
        public readonly ?string $fromState = null,
        public readonly string $fromCountry = 'US',
        public readonly ?float $length = null,
        public readonly ?float $width = null,
        public readonly ?float $height = null,
        public readonly string $dimensionUnits = 'inches',
        public readonly ?string $confirmation = null,
    ) {}

    /**
     * Create from a cart address.
     */
    public static function fromCartAddress(
        object $address,
        float $weight,
        ?string $carrierCode = null
    ): self {
        return new self(
            toCountry: $address->country?->iso2 ?? $address->country_code ?? 'US',
            toPostalCode: $address->postcode,
            toCity: $address->city,
            toState: $address->state,
            weight: $weight,
            carrierCode: $carrierCode,
            residential: true,
            fromPostalCode: config('shipping.origin.postal_code'),
            fromCity: config('shipping.origin.city'),
            fromState: config('shipping.origin.state'),
            fromCountry: config('shipping.origin.country'),
            length: config('shipping.default_dimensions.length'),
            width: config('shipping.default_dimensions.width'),
            height: config('shipping.default_dimensions.height'),
            confirmation: config('shipping.confirmation'),
        );
    }

    /**
     * Convert to ShipStation API format.
     */
    public function toShipStationFormat(): array
    {
        $data = [
            'carrierCode' => $this->carrierCode,
            'fromPostalCode' => $this->fromPostalCode ?? config('shipping.origin.postal_code'),
            'toCountry' => $this->toCountry,
            'toPostalCode' => $this->toPostalCode,
            'toCity' => $this->toCity,
            'toState' => $this->toState,
            'weight' => [
                'value' => $this->weight,
                'units' => $this->weightUnits,
            ],
            'residential' => $this->residential,
        ];

        // Add dimensions if provided
        if ($this->length && $this->width && $this->height) {
            $data['dimensions'] = [
                'length' => $this->length,
                'width' => $this->width,
                'height' => $this->height,
                'units' => $this->dimensionUnits,
            ];
        }

        // Add confirmation if provided
        if ($this->confirmation && $this->confirmation !== 'none') {
            $data['confirmation'] = $this->confirmation;
        }

        return array_filter($data, fn ($value) => $value !== null);
    }

    /**
     * Get a cache key for this request.
     */
    public function getCacheKey(): string
    {
        return sprintf(
            'shipping_rates:%s:%s:%s:%s:%.2f:%s',
            $this->carrierCode ?? 'all',
            $this->toCountry,
            $this->toPostalCode,
            $this->toState ?? '',
            $this->weight,
            $this->residential ? 'res' : 'com'
        );
    }
}
