<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Country;

class CountryStateSeeder extends Seeder
{
    // Canadian province/territory abbreviations from states JSON (SBZone: 0)
    private const CANADA_ABBREVS = [
        'AB', 'BC', 'MB', 'NB', 'NL', 'NS', 'NT', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT',
    ];

    public function run(): void
    {
        $this->seedCountries();
        $this->seedStates();
    }

    private function seedCountries(): void
    {
        $csvPath = database_path('seeders/data/country.csv');
        $existing = Country::pluck('iso2')->flip();

        $handle = fopen($csvPath, 'r');
        fgetcsv($handle); // skip header row

        while (($row = fgetcsv($handle)) !== false) {
            [$countryId, $iso2, $name, $rank, $groupAtTop, $hide, $sbZone] = $row;

            // Normalize encoding (CSV is extended ASCII)
            $name = mb_convert_encoding($name, 'UTF-8', 'Windows-1252');
            $iso2 = trim($iso2);

            if ($existing->has($iso2)) {
                continue;
            }

            // Force the legacy CountryID as the primary key for frontend compatibility
            DB::table('countries')->insert([
                'id'           => (int) $countryId,
                'name'         => $name,
                'iso2'         => $iso2,
                'iso3'         => null,
                'phonecode'    => null,
                'capital'      => null,
                'currency'     => null,
                'native'       => null,
                'emoji'        => null,
                'emoji_u'      => null,
                'rank'         => (int) $rank,
                'group_at_top' => $this->parseBool($groupAtTop),
                'hide'         => $this->parseBool($hide),
                'sb_zone'      => $sbZone !== '' ? (int) $sbZone : null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        fclose($handle);
    }

    private function parseBool(string $value): bool
    {
        return strtolower(trim($value)) === 'true';
    }

    private function seedStates(): void
    {
        $jsonPath = database_path('seeders/data/states_202602271527.json');
        $data = json_decode(file_get_contents($jsonPath), true);

        $usCountry = Country::where('iso2', 'US')->first();
        $caCountry = Country::where('iso2', 'CA')->first();

        if (! $usCountry || ! $caCountry) {
            $this->command->warn('US or CA country not found. Skipping state seeding.');
            return;
        }

        $existingStates = DB::table('states')->pluck('code')->flip();

        foreach ($data['states'] as $state) {
            if ($existingStates->has($state['Abbrev'])) {
                continue;
            }

            $isCanada = in_array($state['Abbrev'], self::CANADA_ABBREVS);
            $countryId = $isCanada ? $caCountry->id : $usCountry->id;

            DB::table('states')->insert([
                'country_id' => $countryId,
                'name'       => $state['State'],
                'code'       => $state['Abbrev'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
