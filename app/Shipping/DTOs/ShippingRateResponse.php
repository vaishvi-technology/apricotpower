<?php

namespace App\Shipping\DTOs;

use Illuminate\Contracts\Support\Arrayable;

class ShippingRateResponse implements Arrayable
{
    public function __construct(
        public readonly string $carrierCode,
        public readonly string $serviceCode,
        public readonly string $serviceName,
        public readonly float $shipmentCost,
        public readonly float $otherCost = 0,
        public readonly ?string $carrierNickname = null,
        public readonly ?int $transitDays = null,
        public readonly ?string $deliveryDate = null,
        public readonly bool $isFreeShipping = false,
    ) {}

    /**
     * Create from ShipStation API response.
     */
    public static function fromShipStation(array $data): self
    {
        return new self(
            carrierCode: $data['carrierCode'] ?? '',
            serviceCode: $data['serviceCode'] ?? '',
            serviceName: $data['serviceName'] ?? '',
            shipmentCost: (float) ($data['shipmentCost'] ?? 0),
            otherCost: (float) ($data['otherCost'] ?? 0),
            carrierNickname: $data['carrierNickname'] ?? null,
            transitDays: isset($data['transitDays']) ? (int) $data['transitDays'] : null,
            deliveryDate: $data['deliveryDate'] ?? null,
        );
    }

    /**
     * Create a free shipping rate.
     */
    public static function freeShipping(string $serviceName = 'Free Shipping'): self
    {
        return new self(
            carrierCode: 'free',
            serviceCode: 'free_shipping',
            serviceName: $serviceName,
            shipmentCost: 0,
            otherCost: 0,
            isFreeShipping: true,
        );
    }

    /**
     * Get the total cost.
     */
    public function getTotalCost(): float
    {
        return $this->shipmentCost + $this->otherCost;
    }

    /**
     * Get the identifier for this rate option.
     */
    public function getIdentifier(): string
    {
        return sprintf('%s:%s', $this->carrierCode, $this->serviceCode);
    }

    /**
     * Get display-friendly carrier name.
     */
    public function getCarrierDisplayName(): string
    {
        return match ($this->carrierCode) {
            'ups' => 'UPS',
            'usps', 'stamps_com' => 'USPS',
            'fedex' => 'FedEx',
            'free' => 'Free',
            default => $this->carrierNickname ?? strtoupper($this->carrierCode),
        };
    }

    /**
     * Get estimated delivery string.
     */
    public function getEstimatedDelivery(): ?string
    {
        if ($this->deliveryDate) {
            return $this->deliveryDate;
        }

        if ($this->transitDays) {
            return $this->transitDays === 1
                ? '1 business day'
                : "{$this->transitDays} business days";
        }

        return null;
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'carrier_code' => $this->carrierCode,
            'service_code' => $this->serviceCode,
            'service_name' => $this->serviceName,
            'carrier_display_name' => $this->getCarrierDisplayName(),
            'shipment_cost' => $this->shipmentCost,
            'other_cost' => $this->otherCost,
            'total_cost' => $this->getTotalCost(),
            'transit_days' => $this->transitDays,
            'estimated_delivery' => $this->getEstimatedDelivery(),
            'identifier' => $this->getIdentifier(),
            'is_free_shipping' => $this->isFreeShipping,
        ];
    }
}
