<?php

namespace App\Modifiers;

use App\Shipping\DTOs\ShippingRateRequest;
use App\Shipping\DTOs\ShippingRateResponse;
use App\Shipping\Exceptions\ShipStationException;
use App\Shipping\ShipStationService;
use Illuminate\Support\Facades\Log;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\TaxClass;

class ShippingModifier
{
    public function __construct(
        protected ShipStationService $shipStation
    ) {}

    public function handle(Cart $cart, \Closure $next)
    {
        // Check if we should use ShipStation or fallback to basic shipping
        if (config('shipping.provider') === 'shipstation') {
            $this->addShipStationOptions($cart);
        } else {
            $this->addBasicShippingOption($cart);
        }

        return $next($cart);
    }

    /**
     * Add shipping options from ShipStation API.
     */
    protected function addShipStationOptions(Cart $cart): void
    {
        $shippingAddress = $cart->shippingAddress;

        // If no shipping address, don't fetch rates yet
        if (! $shippingAddress || ! $shippingAddress->postcode) {
            return;
        }

        try {
            $totalWeight = $this->calculateCartWeight($cart);

            // Check for free shipping eligibility
            if ($this->isEligibleForFreeShipping($cart, $totalWeight)) {
                ShippingManifest::addOption(
                    new ShippingOption(
                        name: 'Free Shipping',
                        description: 'Free ground shipping',
                        identifier: 'free:free_shipping',
                        price: new Price(0, $cart->currency, 1),
                        taxClass: TaxClass::getDefault()
                    )
                );

                return;
            }

            // Build rate request
            $request = ShippingRateRequest::fromCartAddress(
                $shippingAddress,
                $totalWeight
            );

            // Fetch rates from ShipStation
            $rates = $this->shipStation->getRates($request);

            if ($rates->isEmpty()) {
                // No rates available, add a fallback option
                $this->addFallbackShippingOption($cart);

                return;
            }

            // Add each rate as a shipping option
            foreach ($rates as $rate) {
                /** @var ShippingRateResponse $rate */
                ShippingManifest::addOption(
                    new ShippingOption(
                        name: $rate->serviceName,
                        description: $this->buildRateDescription($rate),
                        identifier: $rate->getIdentifier(),
                        price: new Price(
                            (int) ($rate->getTotalCost() * 100), // Convert to cents
                            $cart->currency,
                            1
                        ),
                        taxClass: TaxClass::getDefault(),
                        meta: [
                            'carrier_code' => $rate->carrierCode,
                            'service_code' => $rate->serviceCode,
                            'carrier_name' => $rate->getCarrierDisplayName(),
                            'transit_days' => $rate->transitDays,
                        ]
                    )
                );
            }
        } catch (ShipStationException $e) {
            Log::error('ShipStation rate fetch failed', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->id,
            ]);

            // Add fallback shipping option on API failure
            $this->addFallbackShippingOption($cart);
        }
    }

    /**
     * Calculate total weight of cart in ounces.
     */
    protected function calculateCartWeight(Cart $cart): float
    {
        $totalWeight = 0;

        foreach ($cart->lines as $line) {
            $purchasable = $line->purchasable;

            if ($purchasable && isset($purchasable->weight_value)) {
                // Assuming weight is stored in grams, convert to ounces
                $weightInOunces = $purchasable->weight_value * 0.035274;
                $totalWeight += ($weightInOunces * $line->quantity);
            }
        }

        // Minimum weight of 1 ounce
        return max($totalWeight, 1);
    }

    /**
     * Check if cart is eligible for free shipping.
     */
    protected function isEligibleForFreeShipping(Cart $cart, float $weight): bool
    {
        if (! config('shipping.free_shipping.enabled', false)) {
            return false;
        }

        // Check promo code free shipping
        if ($cart->promo_free_shipping ?? false) {
            return true;
        }

        // Check weight-based free shipping
        $customer = $cart->customer;
        $isRetailer = $customer && $this->isRetailerCustomer($customer);

        $weightLimit = $isRetailer
            ? config('shipping.free_shipping.retailer_weight_limit')
            : config('shipping.free_shipping.consumer_weight_limit');

        return $weight >= $weightLimit;
    }

    /**
     * Check if customer is a retailer.
     */
    protected function isRetailerCustomer($customer): bool
    {
        // Check customer group - AccountGroupID 2 = Retailer/Wholesaler
        $customerGroups = $customer->customerGroups ?? collect();

        foreach ($customerGroups as $group) {
            if (in_array($group->handle ?? $group->id, ['retailer', 'wholesaler', '2'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build a description string for a rate.
     */
    protected function buildRateDescription(ShippingRateResponse $rate): string
    {
        $parts = [$rate->getCarrierDisplayName()];

        if ($rate->transitDays) {
            $parts[] = "({$rate->getEstimatedDelivery()})";
        }

        return implode(' ', $parts);
    }

    /**
     * Add fallback shipping option when ShipStation is unavailable.
     */
    protected function addFallbackShippingOption(Cart $cart): void
    {
        ShippingManifest::addOption(
            new ShippingOption(
                name: 'Standard Shipping',
                description: 'Standard ground shipping (5-7 business days)',
                identifier: 'fallback:standard',
                price: new Price(995, $cart->currency, 1), // $9.95 default
                taxClass: TaxClass::getDefault()
            )
        );
    }

    /**
     * Add basic shipping option (non-ShipStation mode).
     */
    protected function addBasicShippingOption(Cart $cart): void
    {
        if (config('shipping-tables.enabled') == false) {
            ShippingManifest::addOption(
                new ShippingOption(
                    name: 'Basic Delivery',
                    description: 'Basic Delivery',
                    identifier: 'BASDEL',
                    price: new Price(500, $cart->currency, 1),
                    taxClass: TaxClass::getDefault()
                )
            );
        }
    }
}
