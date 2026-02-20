<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyIdMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'legacy_id',
        'new_id',
        'legacy_data',
    ];

    protected function casts(): array
    {
        return [
            'legacy_data' => 'array',
        ];
    }

    public function scopeForEntity($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    public static function findNewId(string $entityType, int $legacyId): ?int
    {
        $mapping = static::where('entity_type', $entityType)
            ->where('legacy_id', $legacyId)
            ->first();

        return $mapping?->new_id;
    }

    public static function findLegacyId(string $entityType, int $newId): ?int
    {
        $mapping = static::where('entity_type', $entityType)
            ->where('new_id', $newId)
            ->first();

        return $mapping?->legacy_id;
    }

    public static function createMapping(string $entityType, int $legacyId, int $newId, ?array $legacyData = null): self
    {
        return static::create([
            'entity_type' => $entityType,
            'legacy_id' => $legacyId,
            'new_id' => $newId,
            'legacy_data' => $legacyData,
        ]);
    }

    public static function getOrCreateMapping(string $entityType, int $legacyId, int $newId, ?array $legacyData = null): self
    {
        return static::firstOrCreate(
            [
                'entity_type' => $entityType,
                'legacy_id' => $legacyId,
            ],
            [
                'new_id' => $newId,
                'legacy_data' => $legacyData,
            ]
        );
    }
}
