<?php

namespace App\Services;

use App\Models\IntegrationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KlaviyoService
{
    protected string $baseUrl;
    protected string $revision;
    protected ?string $privateKey;

    public function __construct()
    {
        $this->baseUrl = config('klaviyo.base_url');
        $this->revision = config('klaviyo.revision');
        $this->privateKey = config('klaviyo.private_key');
    }

    /**
     * Check if Klaviyo integration is enabled and configured.
     */
    public function isEnabled(): bool
    {
        return config('klaviyo.enabled') && !empty($this->privateKey);
    }

    /**
     * Create or update a profile in Klaviyo.
     */
    public function upsertProfile(array $attributes): ?string
    {
        $payload = [
            'data' => [
                'type' => 'profile',
                'attributes' => $attributes,
            ],
        ];

        $response = $this->post('/profiles', $payload);

        return $response['data']['id'] ?? null;
    }

    /**
     * Subscribe a profile to the default Klaviyo list.
     */
    public function subscribeToList(string $email, ?string $listId = null): bool
    {
        $listId = $listId ?? config('klaviyo.list_id');

        $payload = [
            'data' => [
                'type' => 'profile-subscription-bulk-create-job',
                'attributes' => [
                    'profiles' => [
                        'data' => [
                            [
                                'type' => 'profile',
                                'attributes' => [
                                    'email' => $email,
                                    'subscriptions' => [
                                        'email' => [
                                            'marketing' => [
                                                'consent' => 'SUBSCRIBED',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'relationships' => [
                    'list' => [
                        'data' => [
                            'type' => 'list',
                            'id' => $listId,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post('/profile-subscription-bulk-create-jobs', $payload);

        return $response !== null;
    }

    /**
     * Unsubscribe a profile from the default Klaviyo list.
     */
    public function unsubscribeFromList(string $email, ?string $listId = null): bool
    {
        $listId = $listId ?? config('klaviyo.list_id');

        $payload = [
            'data' => [
                'type' => 'profile-subscription-bulk-delete-job',
                'attributes' => [
                    'profiles' => [
                        'data' => [
                            [
                                'type' => 'profile',
                                'attributes' => [
                                    'email' => $email,
                                ],
                            ],
                        ],
                    ],
                ],
                'relationships' => [
                    'list' => [
                        'data' => [
                            'type' => 'list',
                            'id' => $listId,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post('/profile-subscription-bulk-delete-jobs', $payload);

        return $response !== null;
    }

    /**
     * Track an event in Klaviyo (e.g., Placed Order, Added to Cart).
     */
    public function trackEvent(string $metricName, string $email, array $properties = [], ?float $value = null): bool
    {
        $attributes = [
            'metric' => [
                'data' => [
                    'type' => 'metric',
                    'attributes' => [
                        'name' => $metricName,
                    ],
                ],
            ],
            'profile' => [
                'data' => [
                    'type' => 'profile',
                    'attributes' => [
                        'email' => $email,
                    ],
                ],
            ],
            'properties' => $properties,
        ];

        if ($value !== null) {
            $attributes['value'] = $value;
        }

        $payload = [
            'data' => [
                'type' => 'event',
                'attributes' => $attributes,
            ],
        ];

        $response = $this->post('/events', $payload);

        return $response !== null;
    }

    /**
     * Make a POST request to the Klaviyo API.
     */
    protected function post(string $endpoint, array $payload): ?array
    {
        if (!$this->isEnabled()) {
            Log::debug('Klaviyo: Integration disabled, skipping API call', [
                'endpoint' => $endpoint,
            ]);

            return null;
        }

        $log = IntegrationLog::create([
            'integration' => 'klaviyo',
            'action' => 'api_call',
            'entity_type' => $endpoint,
            'status' => IntegrationLog::STATUS_PENDING,
            'request_data' => $payload,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Klaviyo-API-Key ' . $this->privateKey,
                'revision' => $this->revision,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . $endpoint, $payload);

            if ($response->successful()) {
                $log->markSuccess($response->json() ?? []);

                return $response->json() ?? [];
            }

            $log->markFailed(
                "HTTP {$response->status()}: {$response->body()}",
                $response->json() ?? []
            );

            Log::error('Klaviyo API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            $log->markFailed($e->getMessage());

            Log::error('Klaviyo API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Make a GET request to the Klaviyo API.
     */
    protected function get(string $endpoint, array $query = []): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Klaviyo-API-Key ' . $this->privateKey,
                'revision' => $this->revision,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . $endpoint, $query);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Klaviyo API GET error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Klaviyo API GET exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
