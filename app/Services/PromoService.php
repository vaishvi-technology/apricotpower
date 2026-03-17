<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\PromoRule;
use App\Models\PromoUsage;
use Illuminate\Support\Facades\DB;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;

class PromoService
{
    /**
     * Apply a promo to a cart by coupon code.
     *
     * @return array{success: bool, message: string, promo: ?Promo}
     */
    public function applyByCouponCode(Cart $cart, string $couponCode): array
    {
        $couponCode = strtoupper(trim($couponCode));

        if (empty($couponCode)) {
            return ['success' => false, 'message' => 'Please enter a promo code.', 'promo' => null];
        }

        $promo = Promo::where(DB::raw('UPPER(coupon_code)'), $couponCode)->first();

        if (!$promo) {
            return ['success' => false, 'message' => 'Invalid promo code.', 'promo' => null];
        }

        return $this->apply($cart, $promo);
    }

    /**
     * Apply a promo to a cart.
     *
     * @return array{success: bool, message: string, promo: ?Promo}
     */
    public function apply(Cart $cart, Promo $promo): array
    {
        // First, unapply any existing promo
        $this->unapply($cart);

        // Validate the promo
        $validation = $this->validate($promo, $cart);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['reason'], 'promo' => null];
        }

        // Add auto-cart items if configured
        $this->addAutoCartItems($cart, $promo);

        // Find the first applicable rule
        $ruleResult = $this->getFirstApplicableRule($promo, $cart);
        if (!$ruleResult['rule']) {
            $reasons = $ruleResult['reasons'];
            $message = !empty($reasons) ? implode(' ', $reasons) : 'This promo code cannot be applied to your current cart.';
            return ['success' => false, 'message' => $message, 'promo' => null];
        }

        // Apply the rule actions
        $result = $this->assignRule($cart, $promo, $ruleResult['rule']);

        // Save promo to cart
        $cart->update([
            'promo_id' => $promo->id,
            'promo_code' => $promo->coupon_code,
            'promo_discount' => $result['discount'],
            'promo_free_shipping' => $result['free_shipping'],
        ]);

        $promoTitle = $promo->title ?: $promo->name;

        return ['success' => true, 'message' => "Promo \"{$promoTitle}\" applied successfully!", 'promo' => $promo];
    }

    /**
     * Remove a promo from a cart. Mirrors .NET Promo_Unapply + Promo_Unassign.
     */
    public function unapply(Cart $cart): void
    {
        // Remove free/promo cart lines (mirrors Cart_ClearPromoItems)
        $this->clearPromoItems($cart);

        $cart->update([
            'promo_id' => null,
            'promo_code' => null,
            'promo_discount' => 0,
            'promo_free_shipping' => false,
        ]);
    }

    /**
     * Remove all promo-added cart lines.
     * Mirrors .NET Cart_ClearPromoItems().
     */
    protected function clearPromoItems(Cart $cart): void
    {
        // Delete cart lines flagged as promo items via meta
        foreach ($cart->lines as $line) {
            $meta = $line->meta ?? [];
            if (!empty($meta['is_promo_item'])) {
                $line->delete();
            }
        }

        // Refresh lines
        $cart->load('lines');
    }

    /**
     * Add auto-cart items when promo is applied.
     * Mirrors .NET Promo_AddAutoCartItems().
     */
    protected function addAutoCartItems(Cart $cart, Promo $promo): void
    {
        $autocartItems = $promo->autocart_items_parsed;
        if (empty($autocartItems)) {
            return;
        }

        foreach ($autocartItems as $variantId => $requiredQty) {
            $variant = \Lunar\Models\ProductVariant::find($variantId);
            if (!$variant) {
                continue;
            }

            // Check if item already in cart
            $existingLine = $cart->lines->first(function ($line) use ($variantId) {
                return $line->purchasable_id === $variantId
                    && $line->purchasable_type === \Lunar\Models\ProductVariant::class;
            });

            if ($existingLine) {
                // Ensure minimum quantity
                if ($existingLine->quantity < $requiredQty) {
                    $existingLine->update(['quantity' => $requiredQty]);
                }
            } else {
                // Add to cart
                $cart->lines()->create([
                    'purchasable_type' => \Lunar\Models\ProductVariant::class,
                    'purchasable_id' => $variantId,
                    'quantity' => $requiredQty,
                    'meta' => ['is_autocart_item' => true],
                ]);
            }
        }

        $cart->load('lines');
    }

    /**
     * Validate a promo for use.
     * Mirrors .NET oPromo.GetResult() validation logic.
     *
     * @return array{valid: bool, reason: string}
     */
    public function validate(Promo $promo, Cart $cart): array
    {
        // Check active
        if (!$promo->is_active) {
            return ['valid' => false, 'reason' => "The promo \"{$promo->name}\" is not currently active."];
        }

        // Check date range
        if ($promo->valid_start && $promo->valid_start->isFuture()) {
            return ['valid' => false, 'reason' => "The promo \"{$promo->name}\" will not be active until {$promo->valid_start->format('m/d/Y')}."];
        }

        if ($promo->valid_end && $promo->valid_end->isPast()) {
            return ['valid' => false, 'reason' => "The promo \"{$promo->name}\" expired on {$promo->valid_end->format('m/d/Y')}."];
        }

        // Check total usage limit
        if ($promo->hasReachedTotalLimit()) {
            return ['valid' => false, 'reason' => "The promo \"{$promo->name}\" has been used the maximum number of times."];
        }

        // Check per-customer usage limit
        $customerId = $cart->customer_id ?? null;
        $customerEmail = null;

        if ($customerId) {
            $customer = \App\Models\Customer::find($customerId);
            $customerEmail = $customer?->email;
        }

        if ($promo->hasReachedCustomerLimit($customerId, $customerEmail)) {
            return ['valid' => false, 'reason' => "The promo \"{$promo->name}\" has been used the maximum number of times by this account."];
        }

        // Check account group restrictions
        if (!empty($promo->allowed_account_groups)) {
            if ($customerId) {
                $customer = $customer ?? \App\Models\Customer::find($customerId);
                $customerGroupIds = [];

                // Get customer's group IDs
                if ($customer && method_exists($customer, 'customerGroups')) {
                    $customerGroupIds = $customer->customerGroups()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
                }

                $allowedGroups = $promo->allowed_account_groups;
                $hasMatch = !empty(array_intersect($customerGroupIds, $allowedGroups));

                if (!$hasMatch) {
                    return ['valid' => false, 'reason' => "The promo \"{$promo->name}\" cannot be used with your account type."];
                }
            }
        }

        // Check country restrictions
        if (!empty($promo->allowed_countries)) {
            $shippingAddress = $cart->shippingAddress;
            if ($shippingAddress) {
                $country = $shippingAddress->country?->iso2 ?? $shippingAddress->country_code ?? null;
                if ($country && !in_array($country, $promo->allowed_countries)) {
                    return ['valid' => false, 'reason' => "The promo \"{$promo->name}\" cannot be used when shipping to {$country}."];
                }
            }
        }

        return ['valid' => true, 'reason' => ''];
    }

    /**
     * Find the first applicable rule for a promo and cart.
     * Mirrors .NET PromoRule_GetFirstApplicable().
     *
     * @return array{rule: ?PromoRule, reasons: array}
     */
    public function getFirstApplicableRule(Promo $promo, Cart $cart): array
    {
        $reasons = [];
        $rules = $promo->rules()->orderBy('sort_order')->get();

        if ($rules->isEmpty()) {
            return ['rule' => null, 'reasons' => ['This promo has no rules configured.']];
        }

        foreach ($rules as $rule) {
            $result = $this->evaluateRuleConditions($rule, $cart);
            if ($result['passes']) {
                return ['rule' => $rule, 'reasons' => []];
            }
            $reasons[] = $result['reason'];
        }

        return ['rule' => null, 'reasons' => $reasons];
    }

    /**
     * Evaluate all conditions for a rule against the cart.
     * Mirrors .NET Promo_GetRuleResult().
     *
     * @return array{passes: bool, reason: string}
     */
    public function evaluateRuleConditions(PromoRule $rule, Cart $cart): array
    {
        $cart = $cart->calculate();

        // Check items condition
        if ($rule->cond_is_items) {
            $result = $this->checkItemsCondition($rule, $cart);
            if (!$result['passes']) {
                return $result;
            }
        }

        // Check subtotal condition
        if ($rule->cond_is_subtotal) {
            $result = $this->checkSubtotalCondition($rule, $cart);
            if (!$result['passes']) {
                return $result;
            }
        }

        // Check weight condition
        if ($rule->cond_is_weight) {
            $result = $this->checkWeightCondition($rule, $cart);
            if (!$result['passes']) {
                return $result;
            }
        }

        return ['passes' => true, 'reason' => ''];
    }

    /**
     * Check if cart contains required items.
     */
    protected function checkItemsCondition(PromoRule $rule, Cart $cart): array
    {
        $requiredItemIds = $rule->cond_item_ids;
        if (empty($requiredItemIds)) {
            return ['passes' => true, 'reason' => ''];
        }

        // Build cart product quantities, excluding promo items
        $cartItemQuantities = [];
        foreach ($cart->lines as $line) {
            $meta = $line->meta ?? [];
            if (!empty($meta['is_promo_item'])) {
                continue; // Skip promo items for condition checking
            }
            $productId = $line->purchasable->product_id ?? $line->purchasable_id;
            $cartItemQuantities[$productId] = ($cartItemQuantities[$productId] ?? 0) + $line->quantity;
        }

        $cartProductIds = array_keys($cartItemQuantities);

        if ($rule->cond_item_all) {
            // ALL required items must be in cart
            foreach ($requiredItemIds as $itemId) {
                if (!in_array($itemId, $cartProductIds)) {
                    return ['passes' => false, 'reason' => 'Your cart is missing required items for this promo.'];
                }
                if ($rule->cond_item_quantity > 0 && ($cartItemQuantities[$itemId] ?? 0) < $rule->cond_item_quantity) {
                    return ['passes' => false, 'reason' => "You need at least {$rule->cond_item_quantity} of each required item."];
                }
            }
        } else {
            // ANY required item must be in cart
            $found = false;
            foreach ($requiredItemIds as $itemId) {
                if (in_array($itemId, $cartProductIds)) {
                    if ($rule->cond_item_quantity <= 0 || ($cartItemQuantities[$itemId] ?? 0) >= $rule->cond_item_quantity) {
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                return ['passes' => false, 'reason' => 'Your cart does not contain any eligible items for this promo.'];
            }
        }

        return ['passes' => true, 'reason' => ''];
    }

    /**
     * Check if cart subtotal meets the requirement.
     * Uses non-promo item total only, like .NET Cart_GetItemTotal(cartID, False).
     */
    protected function checkSubtotalCondition(PromoRule $rule, Cart $cart): array
    {
        $subtotal = $this->getNonPromoSubtotal($cart);

        if ($rule->cond_subtotal_min > 0 && $subtotal < $rule->cond_subtotal_min) {
            return [
                'passes' => false,
                'reason' => "Your cart subtotal must be at least $" . number_format($rule->cond_subtotal_min, 2) . ".",
            ];
        }

        if ($rule->cond_subtotal_max > 0 && $subtotal > $rule->cond_subtotal_max) {
            return [
                'passes' => false,
                'reason' => "Your cart subtotal must be $" . number_format($rule->cond_subtotal_max, 2) . " or less.",
            ];
        }

        return ['passes' => true, 'reason' => ''];
    }

    /**
     * Check if cart weight meets the requirement.
     * Uses non-promo item weight only, like .NET Cart_GetWeightTotalInOZ(cartID, False).
     */
    protected function checkWeightCondition(PromoRule $rule, Cart $cart): array
    {
        $totalWeight = $this->getNonPromoWeight($cart);

        if ($rule->cond_weight_greater_than) {
            if ($totalWeight < $rule->cond_weight_amount) {
                return [
                    'passes' => false,
                    'reason' => "Your cart weight must be at least {$rule->cond_weight_amount} oz.",
                ];
            }
        } else {
            if ($totalWeight > $rule->cond_weight_amount) {
                return [
                    'passes' => false,
                    'reason' => "Your cart weight must be {$rule->cond_weight_amount} oz or less.",
                ];
            }
        }

        return ['passes' => true, 'reason' => ''];
    }

    /**
     * Get subtotal excluding promo items (mirrors .NET Cart_GetItemTotal(cartID, False)).
     */
    protected function getNonPromoSubtotal(Cart $cart): float
    {
        $subtotal = 0;
        foreach ($cart->lines as $line) {
            $meta = $line->meta ?? [];
            if (!empty($meta['is_promo_item'])) {
                continue;
            }
            $unitPrice = $line->unitPrice->value ?? 0;
            $subtotal += ($unitPrice / 100) * $line->quantity;
        }
        return $subtotal;
    }

    /**
     * Get total weight excluding promo items in oz (mirrors .NET Cart_GetWeightTotalInOZ(cartID, False)).
     */
    protected function getNonPromoWeight(Cart $cart): float
    {
        $totalWeight = 0;
        foreach ($cart->lines as $line) {
            $meta = $line->meta ?? [];
            if (!empty($meta['is_promo_item'])) {
                continue;
            }
            $weight = $line->purchasable->weight_value ?? 0;
            $totalWeight += $weight * $line->quantity;
        }
        return $totalWeight;
    }

    /**
     * Assign a rule's actions to the cart and return the result.
     * Mirrors .NET Promo_Assign() which calls AssignDiscount, AssignFreeItems, AssignBOGO.
     *
     * @return array{discount: float, free_shipping: bool}
     */
    public function assignRule(Cart $cart, Promo $promo, PromoRule $rule): array
    {
        $discount = 0;
        $freeShipping = false;

        $cart = $cart->calculate();

        // Discount action (mirrors PromoRule_AssignDiscount)
        if ($rule->act_is_discount) {
            $discount = $this->calculateRuleDiscount($rule, $cart);
        }

        // Free shipping action
        if ($rule->act_is_free_shipping) {
            $freeShipping = true;
        }

        // Free items action (mirrors PromoRule_AssignFreeItems)
        if ($rule->act_is_free_items) {
            $this->assignFreeItems($rule, $cart);
        }

        // BOGO action (mirrors PromoRule_AssignBOGO)
        if ($rule->act_is_bogo) {
            $bogoDiscount = $this->assignBogo($rule, $cart);
            $discount += $bogoDiscount;
        }

        return [
            'discount' => round($discount, 2),
            'free_shipping' => $freeShipping,
        ];
    }

    /**
     * Calculate the discount amount from a rule.
     */
    protected function calculateRuleDiscount(PromoRule $rule, Cart $cart): float
    {
        if ($rule->act_discount_is_for_items && !empty($rule->act_discount_item_ids)) {
            return $this->calculateItemSpecificDiscount($rule, $cart);
        }

        // Whole cart discount
        $subtotal = $this->getNonPromoSubtotal($cart);

        if ($rule->act_discount_is_percent) {
            $discount = $subtotal * ($rule->act_discount_amount / 100);
        } else {
            $discount = (float) $rule->act_discount_amount;
        }

        return round(min($discount, $subtotal), 2);
    }

    /**
     * Calculate discount on specific items.
     * Mirrors .NET PromoRule_AssignDiscount Path B (item-level with limit).
     */
    protected function calculateItemSpecificDiscount(PromoRule $rule, Cart $cart): float
    {
        $targetItemIds = $rule->act_discount_item_ids;
        $discount = 0;
        $itemsDiscounted = 0;

        // Sort lines by price descending (discount highest-priced first, like .NET code)
        $eligibleLines = $cart->lines->filter(function ($line) use ($targetItemIds) {
            $meta = $line->meta ?? [];
            if (!empty($meta['is_promo_item'])) {
                return false;
            }
            $productId = $line->purchasable->product_id ?? $line->purchasable_id;
            return in_array($productId, $targetItemIds);
        })->sortByDesc(function ($line) {
            return $line->unitPrice->value ?? 0;
        });

        foreach ($eligibleLines as $line) {
            $unitPrice = ($line->unitPrice->value ?? 0) / 100;
            $qty = $line->quantity;

            for ($i = 0; $i < $qty; $i++) {
                if ($rule->act_discount_limit > 0 && $itemsDiscounted >= $rule->act_discount_limit) {
                    break 2;
                }

                if ($rule->act_discount_is_percent) {
                    $discount += $unitPrice * ($rule->act_discount_amount / 100);
                } else {
                    $discount += min((float) $rule->act_discount_amount, $unitPrice);
                }

                $itemsDiscounted++;
            }
        }

        return $discount;
    }

    /**
     * Add free items to cart based on rule.
     * Mirrors .NET PromoRule_AssignFreeItems().
     */
    protected function assignFreeItems(PromoRule $rule, Cart $cart): void
    {
        $freeItemIds = $rule->act_free_item_ids;
        if (empty($freeItemIds)) {
            return;
        }

        // Calculate target quantity based on conditions (mirrors .NET targQty logic)
        $targQty = $this->calculateFreeItemTargetQty($rule, $cart);

        if ($targQty <= 0) {
            return;
        }

        // Apply item limit
        if ($rule->act_item_limit > 0) {
            $targQty = min($targQty, $rule->act_item_limit);
        }

        if ($rule->act_item_is_all) {
            // Add ALL items in list
            foreach ($freeItemIds as $variantId) {
                $this->addFreeItemToCart($cart, $variantId, $targQty, 0);
            }
        } else {
            // Add first available item (customer choice not implemented, default to first)
            $variantId = $freeItemIds[0] ?? null;
            if ($variantId) {
                $this->addFreeItemToCart($cart, $variantId, $targQty, 0);
            }
        }

        $cart->load('lines');
    }

    /**
     * Calculate how many free items to give based on conditions.
     * Mirrors .NET targQty calculation in PromoRule_AssignFreeItems.
     */
    protected function calculateFreeItemTargetQty(PromoRule $rule, Cart $cart): int
    {
        $targets = [];

        // Weight-based calculation
        if ($rule->cond_is_weight && $rule->cond_weight_amount > 0) {
            $cartWeight = $this->getNonPromoWeight($cart);
            if (!$rule->cond_weight_greater_than) {
                // "Every X oz" pattern
                $targets[] = (int) floor($cartWeight / $rule->cond_weight_amount);
            } else {
                $targets[] = 1;
            }
        }

        // Subtotal-based calculation
        if ($rule->cond_is_subtotal) {
            $cartSubtotal = $this->getNonPromoSubtotal($cart);
            if ($rule->cond_subtotal_min > 0) {
                $targets[] = (int) floor($cartSubtotal / $rule->cond_subtotal_min);
            } elseif ($rule->cond_subtotal_max > 0) {
                $targets[] = 1;
            }
        }

        // Item-based calculation
        if ($rule->cond_is_items && !empty($rule->cond_item_ids)) {
            $cartItemQty = $this->getConditionItemQuantity($rule, $cart);
            if ($rule->cond_item_quantity > 0 && $cartItemQty > 0) {
                $targets[] = (int) floor($cartItemQty / $rule->cond_item_quantity);
            } else {
                $targets[] = $cartItemQty > 0 ? 1 : 0;
            }
        }

        // Take minimum of all applicable targets (mirrors .NET logic)
        if (empty($targets)) {
            return 1; // No conditions = 1 free item
        }

        return max(0, min($targets));
    }

    /**
     * Get the quantity of condition items in the cart.
     */
    protected function getConditionItemQuantity(PromoRule $rule, Cart $cart): int
    {
        $condItemIds = $rule->cond_item_ids;
        $quantities = [];

        foreach ($cart->lines as $line) {
            $meta = $line->meta ?? [];
            if (!empty($meta['is_promo_item'])) {
                continue;
            }
            $productId = $line->purchasable->product_id ?? $line->purchasable_id;
            if (in_array($productId, $condItemIds)) {
                $quantities[$productId] = ($quantities[$productId] ?? 0) + $line->quantity;
            }
        }

        if (empty($quantities)) {
            return 0;
        }

        if ($rule->cond_item_all) {
            // ALL items required - take minimum quantity
            return min($quantities);
        } else {
            // ANY item - take maximum quantity
            return max($quantities);
        }
    }

    /**
     * Add a free item to the cart.
     * Mirrors .NET Promo_AddFreeItem().
     */
    protected function addFreeItemToCart(Cart $cart, int $variantId, int $quantity, float $price = 0): void
    {
        $variant = \Lunar\Models\ProductVariant::find($variantId);
        if (!$variant) {
            return;
        }

        // Check if free promo item already exists
        $existingLine = $cart->lines->first(function ($line) use ($variantId) {
            $meta = $line->meta ?? [];
            return $line->purchasable_id === $variantId
                && $line->purchasable_type === \Lunar\Models\ProductVariant::class
                && !empty($meta['is_promo_item']);
        });

        if ($existingLine) {
            if ($quantity <= 0) {
                $existingLine->delete();
            } elseif ($existingLine->quantity !== $quantity) {
                $existingLine->update(['quantity' => $quantity]);
            }
        } elseif ($quantity > 0) {
            $cart->lines()->create([
                'purchasable_type' => \Lunar\Models\ProductVariant::class,
                'purchasable_id' => $variantId,
                'quantity' => $quantity,
                'meta' => [
                    'is_promo_item' => true,
                    'promo_price' => $price,
                    'promo_label' => $price <= 0 ? 'FREE PROMO ITEM' : 'PROMO Pricing',
                ],
            ]);
        }
    }

    /**
     * Apply BOGO (Buy One Get One) logic.
     * Mirrors .NET PromoRule_AssignBOGO().
     *
     * @return float The total BOGO discount amount
     */
    protected function assignBogo(PromoRule $rule, Cart $cart): float
    {
        $bogoItemIds = $rule->act_bogo_item_ids;
        if (empty($bogoItemIds)) {
            return 0;
        }

        $totalBogoDiscount = 0;

        // Find eligible cart lines (non-promo only)
        $eligibleLines = $cart->lines->filter(function ($line) use ($bogoItemIds) {
            $meta = $line->meta ?? [];
            if (!empty($meta['is_promo_item'])) {
                return false;
            }
            $productId = $line->purchasable->product_id ?? $line->purchasable_id;
            return in_array($productId, $bogoItemIds);
        });

        foreach ($eligibleLines as $line) {
            $qty = $line->quantity;
            $unitPrice = ($line->unitPrice->value ?? 0) / 100;

            // Calculate how many free items: Buy X, Get Y
            $targQty = (int) floor(floor($qty / $rule->act_bogo_buy_count) * $rule->act_bogo_get_count);

            if ($rule->act_bogo_limit > 0) {
                $targQty = min($targQty, $rule->act_bogo_limit);
            }

            if ($targQty <= 0) {
                continue;
            }

            // Calculate the BOGO price (discount % off)
            $bogoDiscountPercent = $rule->act_bogo_discount; // 100 = free, 50 = half off
            $discountPerItem = $unitPrice * ($bogoDiscountPercent / 100);
            $totalBogoDiscount += $discountPerItem * $targQty;

            // Add BOGO items as promo lines
            $bogoPrice = round($unitPrice - $discountPerItem, 2);
            $this->addFreeItemToCart($cart, $line->purchasable_id, $targQty, $bogoPrice);
        }

        $cart->load('lines');

        return $totalBogoDiscount;
    }

    /**
     * Record promo usage when order is placed.
     */
    public function recordUsage(Promo $promo, ?int $customerId, ?string $customerEmail, ?int $orderId, float $discountAmount, bool $freeShipping = false): PromoUsage
    {
        return PromoUsage::create([
            'promo_id' => $promo->id,
            'customer_id' => $customerId,
            'customer_email' => $customerEmail,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
            'free_shipping' => $freeShipping,
        ]);
    }

    /**
     * Re-validate promo on a cart (e.g., after cart contents change).
     * Mirrors .NET Promo_Verify().
     * Returns false if the promo is no longer valid and was removed.
     */
    public function verify(Cart $cart): bool
    {
        if (!$cart->promo_id) {
            return true;
        }

        $promo = Promo::find($cart->promo_id);
        if (!$promo) {
            $this->unapply($cart);
            return false;
        }

        // Re-add auto-cart items
        $this->addAutoCartItems($cart, $promo);

        // Re-validate
        $validation = $this->validate($promo, $cart);
        if (!$validation['valid']) {
            $this->unapply($cart);
            return false;
        }

        // Re-evaluate rules
        $ruleResult = $this->getFirstApplicableRule($promo, $cart);
        if (!$ruleResult['rule']) {
            $this->unapply($cart);
            return false;
        }

        // Clear existing promo items before reassigning
        $this->clearPromoItems($cart);

        // Recalculate and reassign
        $result = $this->assignRule($cart, $promo, $ruleResult['rule']);

        $cart->update([
            'promo_discount' => $result['discount'],
            'promo_free_shipping' => $result['free_shipping'],
        ]);

        return true;
    }

    /**
     * Try to auto-apply a promo to a cart.
     * Mirrors .NET Promo_AutoApply().
     *
     * @return array{success: bool, message: string, promo: ?Promo}
     */
    public function autoApply(Cart $cart): array
    {
        // Don't override manually applied promos
        if ($cart->promo_id) {
            return ['success' => false, 'message' => 'A promo is already applied.', 'promo' => null];
        }

        $autoPromos = Promo::valid()->auto()->get();

        foreach ($autoPromos as $promo) {
            $result = $this->apply($cart, $promo);
            if ($result['success']) {
                return $result;
            }
        }

        return ['success' => false, 'message' => 'No auto promos applicable.', 'promo' => null];
    }
}
