<?php

namespace App\Shipping;

use App\Shipping\DTOs\ShippingRateRequest;
use App\Shipping\DTOs\ShippingRateResponse;
use App\Shipping\Exceptions\ShipStationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Order;

class ShipStationService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $apiSecret;

    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('shipping.shipstation.base_url', 'https://ssapi.shipstation.com');
        $this->apiKey = config('shipping.shipstation.api_key', '');
        $this->apiSecret = config('shipping.shipstation.api_secret', '');
        $this->timeout = config('shipping.shipstation.timeout', 30);
    }

    /**
     * Get shipping rates from ShipStation.
     *
     * @throws ShipStationException
     */
    public function getRates(ShippingRateRequest $request): Collection
    {
        $this->validateCredentials();

        // Check cache first
        $cacheKey = $request->getCacheKey();
        $cacheTtl = config('shipping.cache.rates_ttl', 900);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($request) {
            return $this->fetchRatesFromApi($request);
        });
    }

    /**
     * Fetch rates directly from ShipStation API (bypassing cache).
     *
     * @throws ShipStationException
     */
    public function fetchRatesFromApi(ShippingRateRequest $request): Collection
    {
        // If no specific carrier, get rates from configured carriers
        if ($request->carrierCode === null) {
            return $this->getRatesFromAllCarriers($request);
        }

        $response = $this->makeRequest('POST', '/shipments/getrates', $request->toShipStationFormat());

        if (! $response->successful()) {
            throw ShipStationException::fromResponse($response);
        }

        return collect($response->json())
            ->map(fn (array $rate) => ShippingRateResponse::fromShipStation($rate))
            ->sortBy(fn (ShippingRateResponse $rate) => $rate->getTotalCost());
    }

    /**
     * Get rates from all configured carriers.
     */
    protected function getRatesFromAllCarriers(ShippingRateRequest $request): Collection
    {
        $carriers = config('shipping.carriers', ['ups', 'stamps_com']);
        $allRates = collect();

        foreach ($carriers as $carrierCode) {
            try {
                $carrierRequest = new ShippingRateRequest(
                    toCountry: $request->toCountry,
                    toPostalCode: $request->toPostalCode,
                    toCity: $request->toCity,
                    toState: $request->toState,
                    weight: $request->weight,
                    weightUnits: $request->weightUnits,
                    carrierCode: $carrierCode,
                    residential: $request->residential,
                    fromPostalCode: $request->fromPostalCode,
                    fromCity: $request->fromCity,
                    fromState: $request->fromState,
                    fromCountry: $request->fromCountry,
                    length: $request->length,
                    width: $request->width,
                    height: $request->height,
                    dimensionUnits: $request->dimensionUnits,
                    confirmation: $request->confirmation,
                );

                $rates = $this->fetchRatesFromApi($carrierRequest);
                $allRates = $allRates->merge($rates);
            } catch (ShipStationException $e) {
                // Log the error but continue with other carriers
                Log::warning("Failed to get rates from carrier {$carrierCode}: {$e->getMessage()}");
            }
        }

        return $allRates->sortBy(fn (ShippingRateResponse $rate) => $rate->getTotalCost());
    }

    /**
     * Get available carriers from ShipStation.
     *
     * @throws ShipStationException
     */
    public function getCarriers(): Collection
    {
        $this->validateCredentials();

        $cacheKey = 'shipstation:carriers';
        $cacheTtl = config('shipping.cache.carriers_ttl', 86400);

        return Cache::remember($cacheKey, $cacheTtl, function () {
            $response = $this->makeRequest('GET', '/carriers');

            if (! $response->successful()) {
                throw ShipStationException::fromResponse($response);
            }

            return collect($response->json());
        });
    }

    /**
     * Get services for a specific carrier.
     *
     * @throws ShipStationException
     */
    public function getServices(string $carrierCode): Collection
    {
        $this->validateCredentials();

        $cacheKey = "shipstation:services:{$carrierCode}";
        $cacheTtl = config('shipping.cache.services_ttl', 86400);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($carrierCode) {
            $response = $this->makeRequest('GET', "/carriers/listservices?carrierCode={$carrierCode}");

            if (! $response->successful()) {
                throw ShipStationException::fromResponse($response);
            }

            return collect($response->json());
        });
    }

    /**
     * Create an order in ShipStation.
     *
     * @throws ShipStationException
     */
    public function createOrder(Order $order): string
    {
        $this->validateCredentials();

        $shippingAddress = $order->shippingAddress;
        $billingAddress = $order->billingAddress;

        $orderData = [
            'orderNumber' => $order->reference,
            'orderDate' => $order->created_at->toIso8601String(),
            'orderStatus' => 'awaiting_shipment',
            'customerEmail' => $shippingAddress?->contact_email ?? $billingAddress?->contact_email,
            'shipTo' => $this->formatAddress($shippingAddress),
            'billTo' => $this->formatAddress($billingAddress ?? $shippingAddress),
            'items' => $order->lines->map(fn ($line) => [
                'name' => $line->description,
                'quantity' => $line->quantity,
                'unitPrice' => $line->unit_price->decimal,
                'sku' => $line->identifier,
            ])->toArray(),
            'amountPaid' => $order->total->decimal,
            'shippingAmount' => $order->shipping_total?->decimal ?? 0,
            'taxAmount' => $order->tax_total?->decimal ?? 0,
            'carrierCode' => $order->meta['shipstation_carrier_code'] ?? null,
            'serviceCode' => $order->meta['shipstation_service_code'] ?? null,
            'weight' => [
                'value' => $this->calculateOrderWeight($order),
                'units' => 'ounces',
            ],
        ];

        // Remove null values
        $orderData = array_filter($orderData, fn ($value) => $value !== null);

        $response = $this->makeRequest('POST', '/orders/createorder', $orderData);

        if (! $response->successful()) {
            throw ShipStationException::fromResponse($response);
        }

        $result = $response->json();

        return (string) $result['orderId'];
    }

    /**
     * Update an existing order in ShipStation.
     *
     * @throws ShipStationException
     */
    public function updateOrder(Order $order): bool
    {
        $this->validateCredentials();

        $ssOrderId = $order->meta['shipstation_order_id'] ?? null;

        if (! $ssOrderId) {
            throw ShipStationException::invalidRequest('Order does not have a ShipStation order ID');
        }

        $shippingAddress = $order->shippingAddress;
        $billingAddress = $order->billingAddress;

        $orderData = [
            'orderId' => (int) $ssOrderId,
            'orderNumber' => $order->reference,
            'shipTo' => $this->formatAddress($shippingAddress),
            'billTo' => $this->formatAddress($billingAddress ?? $shippingAddress),
        ];

        $response = $this->makeRequest('POST', '/orders/createorder', $orderData);

        return $response->successful();
    }

    /**
     * Cancel an order in ShipStation.
     *
     * @throws ShipStationException
     */
    public function cancelOrder(string $ssOrderId): bool
    {
        $this->validateCredentials();

        $response = $this->makeRequest('DELETE', "/orders/{$ssOrderId}");

        return $response->successful();
    }

    /**
     * Get tracking information for an order.
     *
     * @throws ShipStationException
     */
    public function getTracking(string $ssOrderId): ?array
    {
        $this->validateCredentials();

        $response = $this->makeRequest('GET', "/orders/{$ssOrderId}");

        if (! $response->successful()) {
            return null;
        }

        $order = $response->json();

        // Get shipments for this order
        $shipmentsResponse = $this->makeRequest('GET', "/shipments?orderId={$ssOrderId}");

        if ($shipmentsResponse->successful()) {
            $shipments = $shipmentsResponse->json()['shipments'] ?? [];

            if (! empty($shipments)) {
                $latestShipment = $shipments[0];

                return [
                    'tracking_number' => $latestShipment['trackingNumber'] ?? null,
                    'carrier_code' => $latestShipment['carrierCode'] ?? null,
                    'service_code' => $latestShipment['serviceCode'] ?? null,
                    'ship_date' => $latestShipment['shipDate'] ?? null,
                    'voided' => $latestShipment['voided'] ?? false,
                ];
            }
        }

        return null;
    }

    /**
     * Get shipment status by tracking number.
     *
     * @throws ShipStationException
     */
    public function getShipmentStatus(string $trackingNumber): ?string
    {
        $this->validateCredentials();

        $response = $this->makeRequest('GET', "/shipments?trackingNumber={$trackingNumber}");

        if (! $response->successful()) {
            return null;
        }

        $shipments = $response->json()['shipments'] ?? [];

        if (empty($shipments)) {
            return null;
        }

        $shipment = $shipments[0];

        if ($shipment['voided'] ?? false) {
            return 'voided';
        }

        return $shipment['shipmentStatus'] ?? 'unknown';
    }

    /**
     * Format an address for ShipStation.
     */
    protected function formatAddress(?object $address): array
    {
        if (! $address) {
            return [];
        }

        $name = trim(($address->first_name ?? '') . ' ' . ($address->last_name ?? ''));

        return array_filter([
            'name' => $name ?: null,
            'company' => $address->company_name ?? null,
            'street1' => $address->line_one ?? null,
            'street2' => $address->line_two ?? null,
            'street3' => $address->line_three ?? null,
            'city' => $address->city ?? null,
            'state' => $address->state ?? null,
            'postalCode' => $address->postcode ?? null,
            'country' => $address->country?->iso2 ?? $address->country_code ?? 'US',
            'phone' => $address->contact_phone ?? null,
            'residential' => config('shipping.residential_default', true),
        ], fn ($value) => $value !== null);
    }

    /**
     * Calculate total weight for an order.
     */
    protected function calculateOrderWeight(Order $order): float
    {
        $totalWeight = 0;

        foreach ($order->lines as $line) {
            $purchasable = $line->purchasable;
            if ($purchasable && isset($purchasable->weight_value)) {
                $totalWeight += ($purchasable->weight_value * $line->quantity);
            }
        }

        // Convert to ounces if needed (assuming weight is stored in grams)
        // 1 gram = 0.035274 ounces
        return $totalWeight * 0.035274;
    }

    /**
     * Make an API request to ShipStation.
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): Response
    {
        $client = $this->getHttpClient();

        $url = $this->baseUrl . $endpoint;

        Log::debug("ShipStation API Request: {$method} {$endpoint}", [
            'data' => $data,
        ]);

        try {
            $response = match (strtoupper($method)) {
                'GET' => $client->get($url, $data),
                'POST' => $client->post($url, $data),
                'PUT' => $client->put($url, $data),
                'DELETE' => $client->delete($url, $data),
                default => throw new \InvalidArgumentException("Invalid HTTP method: {$method}"),
            };

            Log::debug("ShipStation API Response: {$response->status()}", [
                'body' => $response->json(),
            ]);

            // Handle rate limiting
            if ($response->status() === 429) {
                $retryAfter = (int) $response->header('Retry-After', 60);
                throw ShipStationException::rateLimitExceeded($retryAfter);
            }

            return $response;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw ShipStationException::connectionTimeout();
        }
    }

    /**
     * Get configured HTTP client.
     */
    protected function getHttpClient(): PendingRequest
    {
        return Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Validate that credentials are configured.
     *
     * @throws ShipStationException
     */
    protected function validateCredentials(): void
    {
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            throw ShipStationException::missingConfiguration();
        }
    }

    /**
     * Clear all ShipStation caches.
     */
    public function clearCache(): void
    {
        Cache::forget('shipstation:carriers');

        // Clear service caches for known carriers
        $carriers = config('shipping.carriers', ['ups', 'stamps_com', 'fedex']);
        foreach ($carriers as $carrier) {
            Cache::forget("shipstation:services:{$carrier}");
        }
    }
}
