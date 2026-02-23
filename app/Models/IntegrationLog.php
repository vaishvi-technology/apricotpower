<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IntegrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration',
        'action',
        'entity_type',
        'entity_id',
        'status',
        'request_data',
        'response_data',
        'error_message',
        'retry_count',
        'processed_at',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    protected function casts(): array
    {
        return [
            'request_data' => 'array',
            'response_data' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeForIntegration($query, string $integration)
    {
        return $query->where('integration', $integration);
    }

    public function markSuccess(array $responseData = []): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'response_data' => $responseData,
            'processed_at' => now(),
        ]);
    }

    public function markFailed(string $errorMessage, array $responseData = []): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'response_data' => $responseData,
            'processed_at' => now(),
        ]);
    }

    public function incrementRetry(): void
    {
        $this->increment('retry_count');
    }
}
