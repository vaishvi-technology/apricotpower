<?php

namespace App\Observers;

use App\Models\RetailerProfile;
use App\Services\GeocodingService;

class RetailerProfileObserver
{
    public function __construct(
        protected GeocodingService $geocodingService,
    ) {}

    public function saving(RetailerProfile $profile): void
    {
        $addressFields = ['street', 'city', 'state', 'country'];
        $addressChanged = $profile->isDirty($addressFields);

        // Geocode if address changed or if coordinates are missing
        if ($addressChanged || (is_null($profile->latitude) && $this->hasAddress($profile))) {
            $address = $this->buildAddress($profile);

            if (! empty($address)) {
                $coordinates = $this->geocodingService->geocode($address);

                if ($coordinates) {
                    $profile->latitude = $coordinates['latitude'];
                    $profile->longitude = $coordinates['longitude'];
                }
            }
        }
    }

    protected function hasAddress(RetailerProfile $profile): bool
    {
        return ! empty($profile->street) || ! empty($profile->city) || ! empty($profile->state);
    }

    protected function buildAddress(RetailerProfile $profile): string
    {
        return collect([
            $profile->street,
            $profile->city,
            $profile->state,
            $profile->country,
        ])->filter()->implode(', ');
    }
}
