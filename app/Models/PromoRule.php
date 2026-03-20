<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'promo_id',
        'name',
        'sort_order',
        // Condition: Items
        'cond_is_items',
        'cond_item_list',
        'cond_item_all',
        'cond_item_quantity',
        // Condition: Subtotal
        'cond_is_subtotal',
        'cond_subtotal_min',
        'cond_subtotal_max',
        // Condition: Weight
        'cond_is_weight',
        'cond_weight_amount',
        'cond_weight_greater_than',
        // Action: Discount
        'act_is_discount',
        'act_discount_amount',
        'act_discount_is_percent',
        'act_discount_is_for_items',
        'act_discount_item_list',
        'act_discount_limit',
        // Action: Free Shipping
        'act_is_free_shipping',
        // Action: Free Items
        'act_is_free_items',
        'act_item_is_all',
        'act_item_list',
        'act_item_limit',
        // Action: BOGO
        'act_is_bogo',
        'act_bogo_item_list',
        'act_bogo_buy_count',
        'act_bogo_get_count',
        'act_bogo_discount',
        'act_bogo_limit',
        // Action: LoyaltyLion Points
        'act_is_ll_points',
        'act_ll_points_amount',
    ];

    protected function casts(): array
    {
        return [
            'cond_is_items' => 'boolean',
            'cond_item_all' => 'boolean',
            'cond_item_quantity' => 'integer',
            'cond_is_subtotal' => 'boolean',
            'cond_subtotal_min' => 'decimal:2',
            'cond_subtotal_max' => 'decimal:2',
            'cond_is_weight' => 'boolean',
            'cond_weight_amount' => 'decimal:2',
            'cond_weight_greater_than' => 'boolean',
            'act_is_discount' => 'boolean',
            'act_discount_amount' => 'decimal:2',
            'act_discount_is_percent' => 'boolean',
            'act_discount_is_for_items' => 'boolean',
            'act_discount_limit' => 'integer',
            'act_is_free_shipping' => 'boolean',
            'act_is_free_items' => 'boolean',
            'act_item_is_all' => 'boolean',
            'act_item_limit' => 'integer',
            'act_is_bogo' => 'boolean',
            'act_bogo_buy_count' => 'integer',
            'act_bogo_get_count' => 'integer',
            'act_bogo_discount' => 'decimal:2',
            'act_bogo_limit' => 'integer',
            'act_is_ll_points' => 'boolean',
            'act_ll_points_amount' => 'integer',
        ];
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    /**
     * Parse a comma-separated item list into an array of IDs.
     */
    public function parseItemList(?string $list): array
    {
        if (empty($list)) {
            return [];
        }

        return array_filter(array_map('intval', explode(',', $list)));
    }

    public function getCondItemIdsAttribute(): array
    {
        return $this->parseItemList($this->cond_item_list);
    }

    public function getActDiscountItemIdsAttribute(): array
    {
        return $this->parseItemList($this->act_discount_item_list);
    }

    public function getActFreeItemIdsAttribute(): array
    {
        return $this->parseItemList($this->act_item_list);
    }

    public function getActBogoItemIdsAttribute(): array
    {
        return $this->parseItemList($this->act_bogo_item_list);
    }
}
