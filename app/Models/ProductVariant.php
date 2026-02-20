<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Models\ProductVariant as LunarProductVariant;

class ProductVariant extends LunarProductVariant
{
    /**
     * Custom relationships to standalone models.
     */
    public function inventoryLots(): HasMany
    {
        return $this->hasMany(InventoryLot::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function customerGroupPrices(): HasMany
    {
        return $this->hasMany(CustomerGroupPrice::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
