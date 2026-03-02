<?php

namespace App\Services;

use App\Models\InventoryLot;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Get the current user ID for tracking, or null if not a regular user.
     * The inventory_movements.user_id references the users table, but in the
     * Lunar admin panel, we're logged in as Staff (from lunar_staff table).
     * We return null for staff users since their IDs don't exist in users table.
     */
    protected function getCurrentUserId(): ?int
    {
        // Try to get user from 'web' guard (regular users)
        $user = Auth::guard('web')->user();
        if ($user instanceof User) {
            return $user->id;
        }

        // For staff/admin users, we can't use their ID since it references a different table
        // Return null - the movement will still be tracked, just without a user reference
        return null;
    }

    /**
     * Adjust inventory for a lot (add or subtract).
     */
    public function adjustInventory(InventoryLot $lot, array $data): InventoryMovement
    {
        return DB::transaction(function () use ($lot, $data) {
            $quantityChange = $data['type'] === 'add'
                ? abs($data['quantity'])
                : -abs($data['quantity']);

            $quantityBefore = $lot->quantity;
            $quantityAfter = $quantityBefore + $quantityChange;

            // Update lot quantity (never go below 0)
            $lot->update(['quantity' => max(0, $quantityAfter)]);

            // Record movement
            return InventoryMovement::create([
                'product_id' => $lot->product_id,
                'product_variant_id' => $lot->product_variant_id,
                'inventory_lot_id' => $lot->id,
                'type' => InventoryMovement::TYPE_ADJUSTED,
                'quantity' => $quantityChange,
                'quantity_before' => $quantityBefore,
                'quantity_after' => max(0, $quantityAfter),
                'reason' => $data['reason'] ?? null,
                'user_id' => $this->getCurrentUserId(),
            ]);
        });
    }

    /**
     * Receive new inventory (create a new lot).
     */
    public function receiveInventory(Product $product, array $data): InventoryLot
    {
        return DB::transaction(function () use ($product, $data) {
            $lot = InventoryLot::create([
                'product_id' => $product->id,
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'lot_number' => $data['lot_number'] ?? $this->generateLotNumber(),
                'quantity' => $data['quantity'],
                'cost_per_unit' => $data['cost_per_unit'] ?? null,
                'received_at' => $data['received_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? null,
                'location' => $data['location'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Record movement
            InventoryMovement::create([
                'product_id' => $product->id,
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'inventory_lot_id' => $lot->id,
                'type' => InventoryMovement::TYPE_RECEIVED,
                'quantity' => $data['quantity'],
                'quantity_before' => 0,
                'quantity_after' => $data['quantity'],
                'reason' => $data['reason'] ?? 'Initial receipt',
                'user_id' => $this->getCurrentUserId(),
            ]);

            return $lot;
        });
    }

    /**
     * Record a sale (subtract from inventory using FIFO).
     */
    public function recordSale(Product $product, int $quantity, ?int $variantId = null, $reference = null): void
    {
        DB::transaction(function () use ($product, $quantity, $variantId, $reference) {
            $remainingToDeduct = $quantity;

            // Get available lots ordered by expiration date (FIFO)
            $lots = InventoryLot::where('product_id', $product->id)
                ->when($variantId, fn($q) => $q->where('product_variant_id', $variantId))
                ->inStock()
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->orderBy('expires_at', 'asc')
                ->orderBy('received_at', 'asc')
                ->get();

            foreach ($lots as $lot) {
                if ($remainingToDeduct <= 0) {
                    break;
                }

                $deductFromLot = min($lot->quantity, $remainingToDeduct);
                $quantityBefore = $lot->quantity;
                $quantityAfter = $quantityBefore - $deductFromLot;

                $lot->update(['quantity' => $quantityAfter]);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'inventory_lot_id' => $lot->id,
                    'type' => InventoryMovement::TYPE_SOLD,
                    'quantity' => -$deductFromLot,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reference_type' => $reference ? get_class($reference) : null,
                    'reference_id' => $reference?->id,
                    'reason' => 'Sale',
                    'user_id' => $this->getCurrentUserId(),
                ]);

                $remainingToDeduct -= $deductFromLot;
            }
        });
    }

    /**
     * Get available (non-expired) stock for a product.
     */
    public function getAvailableStock(Product $product, ?int $variantId = null): int
    {
        return InventoryLot::where('product_id', $product->id)
            ->when($variantId, fn($q) => $q->where('product_variant_id', $variantId))
            ->inStock()
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->sum('quantity');
    }

    /**
     * Get total stock (including expired) for a product.
     */
    public function getTotalStock(Product $product, ?int $variantId = null): int
    {
        return InventoryLot::where('product_id', $product->id)
            ->when($variantId, fn($q) => $q->where('product_variant_id', $variantId))
            ->sum('quantity');
    }

    /**
     * Calculate available quantity for a bundle product.
     */
    public function calculateBundleAvailability(Product $bundleProduct): int
    {
        $components = ProductBundle::where('bundle_product_id', $bundleProduct->id)
            ->with('componentProduct')
            ->get();

        if ($components->isEmpty()) {
            // Not a bundle, return direct inventory
            return $this->getAvailableStock($bundleProduct);
        }

        $minAvailable = PHP_INT_MAX;

        foreach ($components as $component) {
            $componentStock = $this->getAvailableStock($component->componentProduct);
            $possibleBundles = floor($componentStock / $component->quantity);
            $minAvailable = min($minAvailable, $possibleBundles);
        }

        return $minAvailable === PHP_INT_MAX ? 0 : (int) $minAvailable;
    }

    /**
     * Generate a unique lot number.
     */
    public function generateLotNumber(): string
    {
        return 'LOT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Get inventory summary for a product.
     */
    public function getInventorySummary(Product $product, ?int $variantId = null): array
    {
        $query = InventoryLot::where('product_id', $product->id)
            ->when($variantId, fn($q) => $q->where('product_variant_id', $variantId));

        $lots = $query->get();

        return [
            'total_stock' => $lots->sum('quantity'),
            'available_stock' => $lots->filter(fn($lot) => !$lot->is_expired && $lot->quantity > 0)->sum('quantity'),
            'expiring_stock' => $lots->filter(fn($lot) => $lot->is_expiring_soon && $lot->quantity > 0)->sum('quantity'),
            'expired_stock' => $lots->filter(fn($lot) => $lot->is_expired && $lot->quantity > 0)->sum('quantity'),
            'lots_count' => $lots->count(),
            'lots_in_stock' => $lots->where('quantity', '>', 0)->count(),
        ];
    }
}
