<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'integration',
        'external_id',
        'metadata',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeForIntegration($query, string $integration)
    {
        return $query->where('integration', $integration);
    }

    public function markSynced(): void
    {
        $this->update(['last_synced_at' => now()]);
    }

    public static function findByExternalId(string $integration, string $externalId): ?self
    {
        return static::where('integration', $integration)
            ->where('external_id', $externalId)
            ->first();
    }
}
