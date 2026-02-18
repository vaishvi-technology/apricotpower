<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategory extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'item_categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Basic info
        'title',
        'image',
        'description',

        // Organization
        'is_hidden',
        'rank',

        // SEO / Meta fields
        'meta_title',
        'meta_description',
        'meta_keywords',

        // Open Graph
        'og_title',
        'og_type',
        'og_image',
        'og_url',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_hidden' => 'boolean',
        'rank' => 'integer',
    ];

    /**
     * Get the items in this category.
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_category_links', 'item_category_id', 'item_id')
            ->withPivot('rank')
            ->withTimestamps();
    }
}
