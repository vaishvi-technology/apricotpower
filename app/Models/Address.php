<?php

namespace App\Models;

use Lunar\Models\Address as LunarAddress;

/**
 * Extends Lunar's Address model with legacy fields.
 *
 * Lunar base fields: id, customer_id, country_id, title, first_name, last_name,
 *                    company_name, line_one, line_two, line_three, city, state, postcode,
 *                    delivery_instructions, contact_email, contact_phone,
 *                    meta, shipping_default, billing_default, timestamps
 * Added fields: label, type, last_used_at
 *
 * Registered via ModelManifest::replace() in AppServiceProvider.
 */
class Address extends LunarAddress
{
    protected $guarded = [];

    protected $casts = [
        'billing_default' => 'boolean',
        'shipping_default' => 'boolean',
        'meta' => \Illuminate\Database\Eloquent\Casts\AsArrayObject::class,
        'last_used_at' => 'datetime',
    ];

    /**
     * Full formatted address string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->line_one,
            $this->line_two,
            $this->line_three,
            $this->city,
            $this->state,
            $this->postcode,
        ]);

        return implode(', ', $parts);
    }
}
