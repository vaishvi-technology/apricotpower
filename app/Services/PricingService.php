<?php

namespace App\Services;

use App\Models\CustomerGroup;
use App\Models\CustomerGroupPrice;
use App\Models\Product;
use App\Models\ProductVariant;
use Lunar\Facades\Pricing;

class PricingService
{
    /**
     * Get the effective price for a product/variant for a customer group.
     *
     * @param Product $product
     * @param ProductVariant $variant
     * @param CustomerGroup|null $customerGroup
     * @param int $quantity Quantity of this product being purchased
     * @param float $cartTotal Total cart value (for cart-total based tiers)
     * @param int $cartProductCount Total number of products in cart (for products_minimum check)
     * @return array{price: float|null, original_price: float|null, source: string, tier_applied: CustomerGroupPrice|null, savings: float, savings_percent: float}
     */
    public function getEffectivePrice(
        Product $product,
        ProductVariant $variant,
        ?CustomerGroup $customerGroup = null,
        int $quantity = 1,
        float $cartTotal = 0,
        int $cartProductCount = 1
    ): array {
        // Get MAP price first (base price from Lunar's prices table)
        $mapPrice = $this->getMapPrice($variant);

        $result = [
            'price' => $mapPrice,
            'original_price' => $mapPrice,
            'source' => 'map',
            'tier_applied' => null,
            'savings' => 0,
            'savings_percent' => 0,
        ];

        if (!$customerGroup) {
            return $result;
        }

        // Check products minimum requirement
        $productsMinimum = $customerGroup->products_minimum ?? 1;
        if ($cartProductCount < $productsMinimum) {
            $result['source'] = 'map_minimum_not_met';
            return $result;
        }

        // Get group pricing for this product
        $groupPrices = CustomerGroupPrice::where('product_id', $product->id)
            ->where('customer_group_id', $customerGroup->id)
            ->active()
            ->orderBy('is_base_price', 'desc')
            ->orderBy('min_quantity', 'desc')
            ->orderBy('cutoff_amount', 'desc')
            ->get();

        if ($groupPrices->isEmpty()) {
            return $result;
        }

        // Get base group price
        $baseGroupPrice = $groupPrices->firstWhere('is_base_price', true);
        $effectiveBasePrice = $baseGroupPrice?->price ?? $mapPrice;

        // Update result with group base price if set
        if ($baseGroupPrice && $baseGroupPrice->price > 0) {
            $result['price'] = (float) $baseGroupPrice->price;
            $result['source'] = 'group_base';
        }

        // Find applicable tier
        $tierPrices = $groupPrices->where('is_base_price', false);
        $applicableTier = null;

        foreach ($tierPrices as $tier) {
            // Skip if tier price is not better than current price
            if ($tier->price >= $result['price']) {
                continue;
            }

            if ($tier->is_by_quantity) {
                // Quantity-based tier
                if ($quantity >= $tier->min_quantity) {
                    if (!$applicableTier || $tier->min_quantity > $applicableTier->min_quantity) {
                        $applicableTier = $tier;
                    }
                }
            } else {
                // Cart-total based tier
                if ($cartTotal >= $tier->cutoff_amount) {
                    if (!$applicableTier || $tier->cutoff_amount > $applicableTier->cutoff_amount) {
                        $applicableTier = $tier;
                    }
                }
            }
        }

        if ($applicableTier && $applicableTier->price < $result['price']) {
            $result['price'] = (float) $applicableTier->price;
            $result['source'] = 'tier';
            $result['tier_applied'] = $applicableTier;
        }

        // Calculate savings
        if ($mapPrice && $result['price'] < $mapPrice) {
            $result['savings'] = round($mapPrice - $result['price'], 2);
            $result['savings_percent'] = round(($result['savings'] / $mapPrice) * 100, 0);
        }

        return $result;
    }

    /**
     * Get the MAP (Minimum Advertised Price) from Lunar's prices table.
     */
    public function getMapPrice(ProductVariant $variant): ?float
    {
        $basePrice = $variant->basePrices()
            ->whereNull('customer_group_id')
            ->where('min_quantity', 1)
            ->first();

        return $basePrice?->price->decimal(rounding: false);
    }

    /**
     * Get all available pricing tiers for a product and customer group.
     *
     * @param Product $product
     * @param CustomerGroup $customerGroup
     * @return array
     */
    public function getAvailableTiers(Product $product, CustomerGroup $customerGroup): array
    {
        $mapPrice = null;
        $variant = $product->variants()->first();
        if ($variant) {
            $mapPrice = $this->getMapPrice($variant);
        }

        $groupPrices = CustomerGroupPrice::where('product_id', $product->id)
            ->where('customer_group_id', $customerGroup->id)
            ->active()
            ->orderBy('is_base_price', 'desc')
            ->orderBy('min_quantity')
            ->orderBy('cutoff_amount')
            ->get();

        $basePrice = $groupPrices->firstWhere('is_base_price', true);
        $effectiveBasePrice = $basePrice?->price ?? $mapPrice;

        $tiers = $groupPrices->where('is_base_price', false)
            ->filter(fn($t) => $t->price < $effectiveBasePrice)
            ->map(function ($tier) use ($effectiveBasePrice) {
                $savings = round($effectiveBasePrice - $tier->price, 2);
                $savingsPercent = $effectiveBasePrice > 0 ? round(($savings / $effectiveBasePrice) * 100, 0) : 0;

                return [
                    'id' => $tier->id,
                    'price' => (float) $tier->price,
                    'is_by_quantity' => $tier->is_by_quantity,
                    'min_quantity' => $tier->min_quantity,
                    'cutoff_amount' => $tier->cutoff_amount,
                    'savings' => $savings,
                    'savings_percent' => $savingsPercent,
                ];
            })
            ->values()
            ->toArray();

        return [
            'map_price' => $mapPrice,
            'group_base_price' => $basePrice?->price,
            'effective_base_price' => $effectiveBasePrice,
            'products_minimum' => $customerGroup->products_minimum ?? 1,
            'tiers' => $tiers,
        ];
    }

    /**
     * Get the next available tier for upselling purposes.
     *
     * @param Product $product
     * @param CustomerGroup $customerGroup
     * @param int $currentQuantity
     * @param float $currentCartTotal
     * @return array|null
     */
    public function getNextTier(
        Product $product,
        CustomerGroup $customerGroup,
        int $currentQuantity,
        float $currentCartTotal
    ): ?array {
        $variant = $product->variants()->first();
        if (!$variant) {
            return null;
        }

        $currentPricing = $this->getEffectivePrice(
            $product,
            $variant,
            $customerGroup,
            $currentQuantity,
            $currentCartTotal
        );

        $groupPrices = CustomerGroupPrice::where('product_id', $product->id)
            ->where('customer_group_id', $customerGroup->id)
            ->active()
            ->tierPrices()
            ->where('price', '<', $currentPricing['price'])
            ->orderBy('min_quantity')
            ->orderBy('cutoff_amount')
            ->get();

        foreach ($groupPrices as $tier) {
            if ($tier->is_by_quantity && $tier->min_quantity > $currentQuantity) {
                return [
                    'type' => 'quantity',
                    'needed' => $tier->min_quantity - $currentQuantity,
                    'new_price' => (float) $tier->price,
                    'savings' => round($currentPricing['price'] - $tier->price, 2),
                    'message' => "Add " . ($tier->min_quantity - $currentQuantity) . " more to get \${$tier->price} each!",
                ];
            } elseif (!$tier->is_by_quantity && $tier->cutoff_amount > $currentCartTotal) {
                $needed = round($tier->cutoff_amount - $currentCartTotal, 2);
                return [
                    'type' => 'cart_total',
                    'needed' => $needed,
                    'new_price' => (float) $tier->price,
                    'savings' => round($currentPricing['price'] - $tier->price, 2),
                    'message' => "Add \${$needed} to your cart to get \${$tier->price} each!",
                ];
            }
        }

        return null;
    }
}
