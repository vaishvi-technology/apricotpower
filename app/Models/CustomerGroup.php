<?php

namespace App\Models;

use Lunar\Models\CustomerGroup as LunarCustomerGroup;

/**
 * Extends Lunar's CustomerGroup model with legacy Apricot Power fields.
 *
 * Lunar base fields: id, name, handle, default, timestamps
 * Added fields: description, discount_percentage, is_wholesale, net_terms_eligible,
 *               net_terms_days, minimum_order_amount, requires_approval, is_active, sort_order
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
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];
}
