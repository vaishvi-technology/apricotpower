<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Lunar\Models\Tag as LunarTag;

class Tag extends LunarTag
{
    /**
     * Get all products that have this tag.
     */
    public function products(): MorphToMany
    {
        $prefix = config('lunar.database.table_prefix');

        return $this->morphedByMany(
            Product::class,
            'taggable',
            "{$prefix}taggables"
        );
    }
}
