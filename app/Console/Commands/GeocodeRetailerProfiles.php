<?php

namespace App\Console\Commands;

use App\Models\RetailerProfile;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class GeocodeRetailerProfiles extends Command
{
    protected $signature = 'retailers:geocode';

    protected $description = 'Geocode all retailer profiles that have addresses but no coordinates';

    public function handle(GeocodingService $geocodingService): int
    {
        $profiles = RetailerProfile::query()
            ->whereNull('latitude')
            ->where(function ($query) {
                $query->whereNotNull('street')
                    ->orWhereNotNull('city')
                    ->orWhereNotNull('state');
            })
            ->get();

        if ($profiles->isEmpty()) {
            $this->info('No retailer profiles need geocoding.');

            return self::SUCCESS;
        }

        $this->info("Geocoding {$profiles->count()} retailer profiles...");

        $bar = $this->output->createProgressBar($profiles->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($profiles as $profile) {
            $address = collect([
                $profile->street,
                $profile->city,
                $profile->state,
                $profile->country,
            ])->filter()->implode(', ');

            if (empty($address)) {
                $bar->advance();
                $this->newLine();
                $this->warn("Skipped profile #{$profile->id} - no address fields.");
                $failed++;

                continue;
            }

            $this->newLine();
            $this->line("Geocoding: {$address}");

            $coordinates = $geocodingService->geocode($address);

            if ($coordinates) {
                $profile->updateQuietly([
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                ]);
                $this->info("  -> {$coordinates['latitude']}, {$coordinates['longitude']}");
                $success++;
            } else {
                $this->error("  -> Failed! Check logs (storage/logs/laravel.log) for details.");
                $failed++;
            }

            $bar->advance();

            // Respect API rate limits
            usleep(100000); // 100ms delay
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Geocoded: {$success}, Failed: {$failed}");

        return self::SUCCESS;
    }
}
