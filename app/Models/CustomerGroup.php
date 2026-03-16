<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Models\CustomerGroup as LunarCustomerGroup;

/**
 * Extends Lunar's CustomerGroup model with legacy Apricot Power fields.
 *
 * Lunar base fields: id, name, handle, default, timestamps
 * Added fields: description, discount_percentage, is_wholesale, net_terms_eligible,
 *               net_terms_days, minimum_order_amount, products_minimum,
 *               default_tier_by_quantity, requires_approval, is_active, sort_order
 *
 * Registered via ModelManifest::replace() in AppServiceProvider.
 */
class CustomerGroup extends LunarCustomerGroup
{
    protected $guarded = [];

    protected $casts = [
        'attribute_data' => \Lunar\Base\Casts\AsAttributeData::class,
        'default' => 'boolean',
        'discount_percentage' => 'decimal:2',
        'is_wholesale' => 'boolean',
        'net_terms_eligible' => 'boolean',
        'minimum_order_amount' => 'decimal:2',
        'products_minimum' => 'integer',
        'default_tier_by_quantity' => 'boolean',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get all customer group prices for this group.
     */
    public function customerGroupPrices(): HasMany
    {
        return $this->hasMany(CustomerGroupPrice::class);
    }

    /**
     * Get active customer group prices for this group.
     */
    public function activeCustomerGroupPrices(): HasMany
    {
        return $this->customerGroupPrices()->active();
    }
}
