<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyIdMapping extends Model
{
    protected $fillable = [
        'legacy_table',
        'legacy_id',
        'new_table',
        'new_id',
    ];

    protected $casts = [
        'legacy_id' => 'integer',
        'new_id' => 'integer',
    ];

    public static function findNewId(string $legacyTable, int $legacyId): ?int
    {
        return static::where('legacy_table', $legacyTable)
            ->where('legacy_id', $legacyId)
            ->value('new_id');
    }

    public static function findLegacyId(string $newTable, int $newId): ?int
    {
        return static::where('new_table', $newTable)
            ->where('new_id', $newId)
            ->value('legacy_id');
    }
}
