<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Shipping\ShipStationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Order;

class ShipStationWebhookController extends Controller
{
    public function __construct(
        protected ShipStationService $shipStation
    ) {}

    /**
     * Handle incoming ShipStation webhooks.
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('ShipStation webhook received', [
            'payload' => $request->all(),
        ]);

        // Verify webhook signature if secret is configured
        if (! $this->verifySignature($request)) {
            Log::warning('ShipStation webhook signature verification failed');

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $resourceType = $request->input('resource_type');
        $resourceUrl = $request->input('resource_url');

        // ShipStation sends a URL to fetch the actual data
        if ($resourceUrl) {
            return $this->processResourceUrl($resourceType, $resourceUrl);
        }

        // Handle direct webhook payload (for testing or different webhook formats)
        return match ($resourceType) {
            'SHIP_NOTIFY' => $this->handleShipNotify($request->all()),
            'ORDER_NOTIFY' => $this->handleOrderNotify($request->all()),
            'ITEM_ORDER_NOTIFY' => $this->handleItemOrderNotify($request->all()),
            default => $this->handleUnknown($resourceType),
        };
    }

    /**
     * Process webhook by fetching data from resource URL.
     */
    protected function processResourceUrl(string $resourceType, string $resourceUrl): JsonResponse
    {
        try {
            // Fetch data from ShipStation's resource URL
            $response = \Illuminate\Support\Facades\Http::withBasicAuth(
                config('shipping.shipstation.api_key'),
                config('shipping.shipstation.api_secret')
            )->get($resourceUrl);

            if (! $response->successful()) {
                Log::error('Failed to fetch ShipStation webhook resource', [
                    'url' => $resourceUrl,
                    'status' => $response->status(),
                ]);

                return response()->json(['error' => 'Failed to fetch resource'], 500);
            }

            $data = $response->json();

            return match ($resourceType) {
                'SHIP_NOTIFY' => $this->handleShipNotify($data),
                'ORDER_NOTIFY' => $this->handleOrderNotify($data),
                'ITEM_ORDER_NOTIFY' => $this->handleItemOrderNotify($data),
                default => $this->handleUnknown($resourceType),
            };
        } catch (\Exception $e) {
            Log::error('ShipStation webhook processing error', [
                'error' => $e->getMessage(),
                'type' => $resourceType,
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle ship notification webhook.
     */
    protected function handleShipNotify(array $data): JsonResponse
    {
        Log::info('Processing ShipStation SHIP_NOTIFY', ['data' => $data]);

        // Handle both single shipment and array of shipments
        $shipments = isset($data['shipments']) ? $data['shipments'] : [$data];

        foreach ($shipments as $shipment) {
            $this->processShipment($shipment);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Process a single shipment update.
     */
    protected function processShipment(array $shipment): void
    {
        $orderNumber = $shipment['orderNumber'] ?? null;
        $ssOrderId = $shipment['orderId'] ?? null;
        $trackingNumber = $shipment['trackingNumber'] ?? null;
        $carrierCode = $shipment['carrierCode'] ?? null;
        $shipDate = $shipment['shipDate'] ?? null;

        if (! $orderNumber && ! $ssOrderId) {
            Log::warning('ShipStation shipment missing order identifier', ['shipment' => $shipment]);

            return;
        }

        // Find the order by ShipStation order ID or order reference
        $order = $ssOrderId
            ? Order::where('shipstation_order_id', $ssOrderId)->first()
            : Order::where('reference', $orderNumber)->first();

        if (! $order) {
            Log::warning('Order not found for ShipStation shipment', [
                'orderNumber' => $orderNumber,
                'ssOrderId' => $ssOrderId,
            ]);

            return;
        }

        // Update order with shipping information
        $order->update([
            'tracking_number' => $trackingNumber,
            'shipstation_carrier_code' => $carrierCode,
            'shipping_status' => 'shipped',
            'shipped_at' => $shipDate ? \Carbon\Carbon::parse($shipDate) : now(),
        ]);

        Log::info('Order updated with shipping information', [
            'order_id' => $order->id,
            'tracking_number' => $trackingNumber,
        ]);

        // Dispatch event for other parts of the system
        event(new \App\Events\OrderShipped($order, $trackingNumber));
    }

    /**
     * Handle order notification webhook.
     */
    protected function handleOrderNotify(array $data): JsonResponse
    {
        Log::info('Processing ShipStation ORDER_NOTIFY', ['data' => $data]);

        // Handle order status changes if needed
        $orders = isset($data['orders']) ? $data['orders'] : [$data];

        foreach ($orders as $orderData) {
            $this->processOrderUpdate($orderData);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Process an order update.
     */
    protected function processOrderUpdate(array $orderData): void
    {
        $orderNumber = $orderData['orderNumber'] ?? null;
        $ssOrderId = $orderData['orderId'] ?? null;
        $orderStatus = $orderData['orderStatus'] ?? null;

        if (! $orderNumber && ! $ssOrderId) {
            return;
        }

        $order = $ssOrderId
            ? Order::where('shipstation_order_id', $ssOrderId)->first()
            : Order::where('reference', $orderNumber)->first();

        if (! $order) {
            return;
        }

        // Map ShipStation order status to our shipping status
        $shippingStatus = match ($orderStatus) {
            'awaiting_payment' => 'pending',
            'awaiting_shipment' => 'processing',
            'shipped' => 'shipped',
            'on_hold' => 'on_hold',
            'cancelled' => 'cancelled',
            default => $order->shipping_status,
        };

        if ($shippingStatus !== $order->shipping_status) {
            $order->update(['shipping_status' => $shippingStatus]);

            Log::info('Order shipping status updated', [
                'order_id' => $order->id,
                'status' => $shippingStatus,
            ]);
        }
    }

    /**
     * Handle item order notification webhook.
     */
    protected function handleItemOrderNotify(array $data): JsonResponse
    {
        Log::info('Processing ShipStation ITEM_ORDER_NOTIFY', ['data' => $data]);

        // This webhook is for item-level shipping
        // Implement if you need to track individual item shipments

        return response()->json(['success' => true]);
    }

    /**
     * Handle unknown webhook type.
     */
    protected function handleUnknown(string $resourceType): JsonResponse
    {
        Log::info('Unknown ShipStation webhook type', ['type' => $resourceType]);

        return response()->json(['success' => true, 'message' => 'Unhandled webhook type']);
    }

    /**
     * Verify webhook signature.
     */
    protected function verifySignature(Request $request): bool
    {
        $secret = config('shipping.shipstation.webhook_secret');

        // If no secret is configured, skip verification
        if (empty($secret)) {
            return true;
        }

        // ShipStation uses a hash in the header for verification
        $signature = $request->header('X-SS-Signature');

        if (empty($signature)) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        return hash_equals($expectedSignature, $signature);
    }
}
