<?php

namespace App\Filament\Pages;

use App\Models\RetailerProfile;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class RetailerLocations extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?string $navigationLabel = 'Retailer Locations';

    protected static ?string $title = 'Retailer Locations';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.retailer-locations';

    public string $filterState = '';

    public string $filterCountry = '';

    public function getRetailersProperty(): Collection
    {
        return RetailerProfile::query()
            ->where('include_in_retailer_map', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
    }

    public function getStatesProperty(): array
    {
        return RetailerProfile::query()
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();
    }

    public function getCountriesProperty(): array
    {
        return RetailerProfile::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();
    }

    public function getRetailerDataProperty(): array
    {
        return $this->retailers->map(fn (RetailerProfile $profile) => [
            'id' => $profile->id,
            'name' => $profile->name ?? 'Unnamed Store',
            'street' => $profile->street,
            'city' => $profile->city,
            'state' => $profile->state,
            'country' => $profile->country,
            'phone' => $profile->phone,
            'website' => $profile->website,
            'latitude' => $profile->latitude,
            'longitude' => $profile->longitude,
        ])->toArray();
    }

    protected function getViewData(): array
    {
        return [
            'retailers' => $this->retailerData,
            'states' => $this->states,
            'countries' => $this->countries,
            'googleMapsApiKey' => config('services.google_maps.key'),
        ];
    }
}
