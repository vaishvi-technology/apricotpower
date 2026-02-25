<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function geocode(string $address): ?array
    {
        $apiKey = config('services.google_maps.key');

        if (empty($apiKey)) {
            Log::warning('Google Maps API key is not configured.');

            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey,
            ]);

            $data = $response->json();

            if (($data['status'] ?? '') === 'OK' && ! empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];

                return [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                ];
            }

            Log::warning("Geocoding failed for address: {$address}", [
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? ($data['results'][0]['error_message'] ?? null),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("Geocoding exception for address: {$address}", [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
