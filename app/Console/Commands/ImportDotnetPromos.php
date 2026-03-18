<?php

namespace App\Console\Commands;

use App\Models\Promo;
use App\Models\PromoRule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportDotnetPromos extends Command
{
    protected $signature = 'import:dotnet-promos {--fresh : Truncate existing promos before import}';

    protected $description = 'Import promo codes from dotnet (apricot_live_dot_net) database into Laravel promos and promo_rules tables';

    public function handle(): int
    {
        $this->info('Starting import from dotnet database...');

        $dotnet = DB::connection('dotnet');

        // Check dotnet connection
        try {
            $dotnet->getPdo();
        } catch (\Exception $e) {
            $this->error('Cannot connect to dotnet database: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Fetch dotnet data
        $coupons = $dotnet->table('coupons')->get();
        $bundles = $dotnet->table('couponbundles')->get()->groupBy('BundleCoupon');
        $couponItems = $dotnet->table('couponitems')->get()->groupBy('CouponID');

        $this->info("Found {$coupons->count()} coupons in dotnet database.");

        if ($coupons->isEmpty()) {
            $this->warn('No coupons found. Nothing to import.');
            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            if ($this->confirm('This will delete all existing promos and promo_rules. Continue?')) {
                PromoRule::truncate();
                Promo::truncate();
                $this->warn('Existing promos truncated.');
            } else {
                $this->info('Aborted.');
                return self::SUCCESS;
            }
        }

        $imported = 0;
        $skipped = 0;

        foreach ($coupons as $coupon) {
            // Skip if coupon_code already exists in promos
            if ($coupon->CouponCode && Promo::where('coupon_code', $coupon->CouponCode)->exists()) {
                $this->warn("Skipping duplicate coupon code: {$coupon->CouponCode}");
                $skipped++;
                continue;
            }

            DB::beginTransaction();

            try {
                // Map dotnet coupon → Laravel promo
                $promo = Promo::create([
                    'name' => $coupon->CouponTitle ?? 'Imported Promo #' . $coupon->CouponID,
                    'title' => $coupon->CouponTitle,
                    'coupon_code' => $coupon->CouponCode ?: null,
                    'description' => $coupon->CouponNote,
                    'is_active' => (bool) $coupon->CouponIsActive,
                    'is_auto' => false,
                    'is_hidden' => false,
                    'landing_url' => null,
                    'valid_start' => $coupon->CouponStartDate,
                    'valid_end' => $coupon->CouponExpirationDate,
                    'limit_per_customer' => $coupon->CouponOneUse ? 1 : 0,
                    'limit_total' => ($coupon->CouponAvailable == -1) ? 0 : max(0, (int) $coupon->CouponAvailable),
                    'account_groups' => $this->mapGroupId($coupon->CouponGroupID),
                    'countries' => null,
                    'autocart_items' => null,
                    'disable_volume_discounts' => false,
                ]);

                // Build promo rule from coupon discount fields
                $this->createDiscountRule($promo, $coupon);

                // Build BOGO rules from couponbundles
                $couponBundles = $bundles->get($coupon->CouponID);
                if ($couponBundles && $couponBundles->isNotEmpty()) {
                    $this->createBundleRule($promo, $coupon, $couponBundles);
                }

                // Build free item rules from couponitems
                $items = $couponItems->get($coupon->CouponID);
                if ($items && $items->isNotEmpty()) {
                    $this->createFreeItemRule($promo, $items);
                }

                DB::commit();
                $imported++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to import coupon {$coupon->CouponID} ({$coupon->CouponCode}): {$e->getMessage()}");
                $skipped++;
            }
        }

        $this->info("Import complete: {$imported} imported, {$skipped} skipped.");

        return self::SUCCESS;
    }

    /**
     * Create the main discount rule from dotnet coupon fields.
     */
    private function createDiscountRule(Promo $promo, object $coupon): void
    {
        $hasDiscount = $coupon->CouponPercentOff || $coupon->CouponDollarOff;
        $hasFreeShipping = (bool) $coupon->CouponEnableFreeShipping;
        $hasSubtotalCondition = $coupon->CouponMinimumAmount && $coupon->CouponMinimumAmount > 0;

        if (!$hasDiscount && !$hasFreeShipping) {
            // Coupon might be purely for bundles/free items, still create a base rule
            if (!$hasSubtotalCondition) {
                return;
            }
        }

        // Parse item restrictions from CouponItems (pipe-delimited: |374||375||376|)
        $itemList = $this->parsePipeList($coupon->CouponItems ?? '');

        $isPercent = !empty($coupon->CouponPercentOff);
        $discountAmount = $isPercent
            ? (float) $coupon->CouponPercentOff
            : (float) ($coupon->CouponDollarOff ?? 0);

        PromoRule::create([
            'promo_id' => $promo->id,
            'name' => 'Discount Rule',
            'sort_order' => 0,
            // Condition: subtotal
            'cond_is_subtotal' => $hasSubtotalCondition,
            'cond_subtotal_min' => $hasSubtotalCondition ? (float) $coupon->CouponMinimumAmount : 0,
            'cond_subtotal_max' => ($coupon->CouponMaximumAmount && $coupon->CouponMaximumAmount > 0)
                ? (float) $coupon->CouponMaximumAmount : 0,
            // Condition: items
            'cond_is_items' => !empty($itemList),
            'cond_item_list' => $itemList ?: null,
            'cond_item_all' => false,
            'cond_item_quantity' => $coupon->CouponNumberOfProducts ? (int) $coupon->CouponNumberOfProducts : 1,
            // Action: discount
            'act_is_discount' => $hasDiscount,
            'act_discount_amount' => $discountAmount,
            'act_discount_is_percent' => $isPercent,
            'act_discount_is_for_items' => !empty($itemList),
            'act_discount_item_list' => $itemList ?: null,
            'act_discount_limit' => 0,
            // Action: free shipping
            'act_is_free_shipping' => $hasFreeShipping,
        ]);
    }

    /**
     * Create BOGO rule from dotnet couponbundles entries.
     */
    private function createBundleRule(Promo $promo, object $coupon, $bundleRows): void
    {
        // Group bundle items: each row has BundleItem and BundleQty
        $itemIds = $bundleRows->pluck('BundleItem')->filter()->implode(',');
        $totalQty = $bundleRows->sum('BundleQty');

        if (empty($itemIds)) {
            return;
        }

        // The dotnet bundle pattern: buy X items, get discount
        // If there's a dollar-off or percent-off, it's applied to the bundle
        $isPercent = !empty($coupon->CouponPercentOff);
        $discount = $isPercent
            ? (float) $coupon->CouponPercentOff
            : 100; // Default BOGO = 100% off the free item

        PromoRule::create([
            'promo_id' => $promo->id,
            'name' => 'Bundle Rule',
            'sort_order' => 1,
            // Condition: items in cart
            'cond_is_items' => true,
            'cond_item_list' => $itemIds,
            'cond_item_all' => false,
            'cond_item_quantity' => max(1, $totalQty),
            // Action: BOGO
            'act_is_bogo' => true,
            'act_bogo_item_list' => $itemIds,
            'act_bogo_buy_count' => max(1, $totalQty),
            'act_bogo_get_count' => 1,
            'act_bogo_discount' => $discount,
            'act_bogo_limit' => 0,
        ]);
    }

    /**
     * Create free item rule from dotnet couponitems entries.
     */
    private function createFreeItemRule(Promo $promo, $itemRows): void
    {
        $itemIds = $itemRows->pluck('ChildItemID')->filter()->implode(',');

        if (empty($itemIds)) {
            return;
        }

        PromoRule::create([
            'promo_id' => $promo->id,
            'name' => 'Free Items Rule',
            'sort_order' => 2,
            // Action: free items
            'act_is_free_items' => true,
            'act_item_is_all' => false,
            'act_item_list' => $itemIds,
            'act_item_limit' => (int) $itemRows->sum('ChildQty'),
        ]);
    }

    /**
     * Parse dotnet pipe-delimited item list (e.g., |374||375||376|) to comma-separated IDs.
     */
    private function parsePipeList(string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $ids = array_filter(array_map('trim', explode('|', $value)));
        $ids = array_filter($ids, fn($id) => is_numeric($id));

        return implode(',', $ids);
    }

    /**
     * Map dotnet CouponGroupID to Laravel account_groups string.
     * GroupID 1 = default/all groups → null (no restriction).
     */
    private function mapGroupId(int $groupId): ?string
    {
        if ($groupId <= 1) {
            return null;
        }

        return (string) $groupId;
    }
}
