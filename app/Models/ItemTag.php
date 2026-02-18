<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lunar\Models\Tag as LunarTag;

class ItemTag extends LunarTag
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'item_tags';

    /**
     * Override getTable to prevent Lunar's automatic prefix.
     */
    public function getTable(): string
    {
        return 'item_tags';
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Basic info
        'value',
        'description',

        // Flags
        'is_stealth',
        'is_hidden',

        // Badge
        'badge_image',
        'badge_description',

        // SEO / Meta fields
        'meta_title',
        'meta_keywords',
        'meta_description',

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
        'is_stealth' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    /**
     * Get the items associated with this tag.
     */
    public function items(): MorphToMany
    {
        return $this->morphedByMany(
            Item::class,
            'taggable',
            'item_tag_links',
            'tag_id',
            'taggable_id'
        )->withTimestamps();
    }
}
