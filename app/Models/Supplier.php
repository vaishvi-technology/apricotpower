<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_name',
        'phone',
        'email',
        'supplier_terms',
        'lead_time',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lead_time' => 'integer',
    ];

    /**
     * Get all products for this supplier.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all inventory lots from this supplier.
     */
    public function inventoryLots(): HasMany
    {
        return $this->hasMany(InventoryLot::class);
    }

    /**
     * Get all incoming shipments from this supplier.
     */
    public function incomingShipments(): HasMany
    {
        return $this->hasMany(IncomingShipment::class);
    }

    /**
     * Scope to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
