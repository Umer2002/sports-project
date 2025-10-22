<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('cities')->truncate();
        Schema::enableForeignKeyConstraints();

        $statesByCode = DB::table('states')
            ->select('id', 'iso2', 'country_code')
            ->get()
            ->keyBy(function ($state) {
                return strtoupper($state->iso2);
            });
        $now = now();

        $cities = [
            ['country_id' => 231, 'state_code' => 'AL', 'name' => 'Birmingham', 'latitude' => 33.5186000, 'longitude' => -86.8104000],
            ['country_id' => 231, 'state_code' => 'AK', 'name' => 'Anchorage', 'latitude' => 61.2173810, 'longitude' => -149.8631290],
            ['country_id' => 231, 'state_code' => 'AZ', 'name' => 'Phoenix', 'latitude' => 33.4483760, 'longitude' => -112.0740360],
            ['country_id' => 231, 'state_code' => 'AR', 'name' => 'Little Rock', 'latitude' => 34.7464830, 'longitude' => -92.2895970],
            ['country_id' => 231, 'state_code' => 'CA', 'name' => 'Los Angeles', 'latitude' => 34.0522350, 'longitude' => -118.2436830],
            ['country_id' => 231, 'state_code' => 'CO', 'name' => 'Denver', 'latitude' => 39.7392360, 'longitude' => -104.9902510],
            ['country_id' => 231, 'state_code' => 'CT', 'name' => 'Hartford', 'latitude' => 41.7658040, 'longitude' => -72.6733720],
            ['country_id' => 231, 'state_code' => 'DE', 'name' => 'Dover', 'latitude' => 39.1581680, 'longitude' => -75.5243680],
            ['country_id' => 231, 'state_code' => 'DC', 'name' => 'Washington', 'latitude' => 38.9071920, 'longitude' => -77.0368730],
            ['country_id' => 231, 'state_code' => 'FL', 'name' => 'Miami', 'latitude' => 25.7616810, 'longitude' => -80.1917880],
            ['country_id' => 231, 'state_code' => 'GA', 'name' => 'Atlanta', 'latitude' => 33.7489970, 'longitude' => -84.3879850],
            ['country_id' => 231, 'state_code' => 'HI', 'name' => 'Honolulu', 'latitude' => 21.3069440, 'longitude' => -157.8583370],
            ['country_id' => 231, 'state_code' => 'ID', 'name' => 'Boise', 'latitude' => 43.6150210, 'longitude' => -116.2023160],
            ['country_id' => 231, 'state_code' => 'IL', 'name' => 'Chicago', 'latitude' => 41.8781130, 'longitude' => -87.6297990],
            ['country_id' => 231, 'state_code' => 'IN', 'name' => 'Indianapolis', 'latitude' => 39.7684020, 'longitude' => -86.1580660],
            ['country_id' => 231, 'state_code' => 'IA', 'name' => 'Des Moines', 'latitude' => 41.5868340, 'longitude' => -93.6250150],
            ['country_id' => 231, 'state_code' => 'KS', 'name' => 'Wichita', 'latitude' => 37.6871760, 'longitude' => -97.3300550],
            ['country_id' => 231, 'state_code' => 'KY', 'name' => 'Louisville', 'latitude' => 38.2526660, 'longitude' => -85.7584530],
            ['country_id' => 231, 'state_code' => 'LA', 'name' => 'New Orleans', 'latitude' => 29.9510650, 'longitude' => -90.0715330],
            ['country_id' => 231, 'state_code' => 'ME', 'name' => 'Portland', 'latitude' => 43.6590990, 'longitude' => -70.2568210],
            ['country_id' => 231, 'state_code' => 'MD', 'name' => 'Baltimore', 'latitude' => 39.2903860, 'longitude' => -76.6121900],
            ['country_id' => 231, 'state_code' => 'MA', 'name' => 'Boston', 'latitude' => 42.3600810, 'longitude' => -71.0588840],
            ['country_id' => 231, 'state_code' => 'MI', 'name' => 'Detroit', 'latitude' => 42.3314290, 'longitude' => -83.0457530],
            ['country_id' => 231, 'state_code' => 'MN', 'name' => 'Minneapolis', 'latitude' => 44.9777530, 'longitude' => -93.2650150],
            ['country_id' => 231, 'state_code' => 'MS', 'name' => 'Jackson', 'latitude' => 32.2987570, 'longitude' => -90.1848100],
            ['country_id' => 231, 'state_code' => 'MO', 'name' => 'Kansas City', 'latitude' => 39.0997240, 'longitude' => -94.5783310],
            ['country_id' => 231, 'state_code' => 'MT', 'name' => 'Billings', 'latitude' => 45.7832870, 'longitude' => -108.5006900],
            ['country_id' => 231, 'state_code' => 'NE', 'name' => 'Omaha', 'latitude' => 41.2565380, 'longitude' => -95.9345020],
            ['country_id' => 231, 'state_code' => 'NV', 'name' => 'Las Vegas', 'latitude' => 36.1699410, 'longitude' => -115.1398320],
            ['country_id' => 231, 'state_code' => 'NH', 'name' => 'Manchester', 'latitude' => 42.9956400, 'longitude' => -71.4547880],
            ['country_id' => 231, 'state_code' => 'NJ', 'name' => 'Newark', 'latitude' => 40.7356570, 'longitude' => -74.1723630],
            ['country_id' => 231, 'state_code' => 'NM', 'name' => 'Albuquerque', 'latitude' => 35.0843850, 'longitude' => -106.6504210],
            ['country_id' => 231, 'state_code' => 'NY', 'name' => 'New York City', 'latitude' => 40.7127760, 'longitude' => -74.0059740],
            ['country_id' => 231, 'state_code' => 'NC', 'name' => 'Charlotte', 'latitude' => 35.2270850, 'longitude' => -80.8431240],
            ['country_id' => 231, 'state_code' => 'ND', 'name' => 'Fargo', 'latitude' => 46.8771860, 'longitude' => -96.7898030],
            ['country_id' => 231, 'state_code' => 'OH', 'name' => 'Columbus', 'latitude' => 39.9611780, 'longitude' => -82.9987950],
            ['country_id' => 231, 'state_code' => 'OK', 'name' => 'Oklahoma City', 'latitude' => 35.4675600, 'longitude' => -97.5164260],
            ['country_id' => 231, 'state_code' => 'OR', 'name' => 'Portland', 'latitude' => 45.5152300, 'longitude' => -122.6783850],
            ['country_id' => 231, 'state_code' => 'PA', 'name' => 'Philadelphia', 'latitude' => 39.9525830, 'longitude' => -75.1652220],
            ['country_id' => 231, 'state_code' => 'RI', 'name' => 'Providence', 'latitude' => 41.8239900, 'longitude' => -71.4128340],
            ['country_id' => 231, 'state_code' => 'SC', 'name' => 'Charleston', 'latitude' => 32.7765660, 'longitude' => -79.9309230],
            ['country_id' => 231, 'state_code' => 'SD', 'name' => 'Sioux Falls', 'latitude' => 43.5445940, 'longitude' => -96.7312700],
            ['country_id' => 231, 'state_code' => 'TN', 'name' => 'Nashville', 'latitude' => 36.1626630, 'longitude' => -86.7816010],
            ['country_id' => 231, 'state_code' => 'TX', 'name' => 'Houston', 'latitude' => 29.7604270, 'longitude' => -95.3698040],
            ['country_id' => 231, 'state_code' => 'UT', 'name' => 'Salt Lake City', 'latitude' => 40.7607800, 'longitude' => -111.8910450],
            ['country_id' => 231, 'state_code' => 'VT', 'name' => 'Burlington', 'latitude' => 44.4758830, 'longitude' => -73.2120740],
            ['country_id' => 231, 'state_code' => 'VA', 'name' => 'Virginia Beach', 'latitude' => 36.8529260, 'longitude' => -75.9779850],
            ['country_id' => 231, 'state_code' => 'WA', 'name' => 'Seattle', 'latitude' => 47.6062090, 'longitude' => -122.3320690],
            ['country_id' => 231, 'state_code' => 'WV', 'name' => 'Charleston', 'latitude' => 38.3498200, 'longitude' => -81.6326220],
            ['country_id' => 231, 'state_code' => 'WI', 'name' => 'Milwaukee', 'latitude' => 43.0389020, 'longitude' => -87.9064710],
            ['country_id' => 231, 'state_code' => 'WY', 'name' => 'Cheyenne', 'latitude' => 41.1402590, 'longitude' => -104.8202360],
            ['country_id' => 38, 'state_code' => 'AB', 'name' => 'Calgary', 'latitude' => 51.0447330, 'longitude' => -114.0718830],
            ['country_id' => 38, 'state_code' => 'BC', 'name' => 'Vancouver', 'latitude' => 49.2827290, 'longitude' => -123.1207380],
            ['country_id' => 38, 'state_code' => 'MB', 'name' => 'Winnipeg', 'latitude' => 49.8951380, 'longitude' => -97.1383740],
            ['country_id' => 38, 'state_code' => 'NB', 'name' => 'Moncton', 'latitude' => 46.0878180, 'longitude' => -64.7782360],
            ['country_id' => 38, 'state_code' => 'NL', 'name' => "St. John's", 'latitude' => 47.5615100, 'longitude' => -52.7125760],
            ['country_id' => 38, 'state_code' => 'NS', 'name' => 'Halifax', 'latitude' => 44.6487640, 'longitude' => -63.5752370],
            ['country_id' => 38, 'state_code' => 'NT', 'name' => 'Yellowknife', 'latitude' => 62.4540270, 'longitude' => -114.3717880],
            ['country_id' => 38, 'state_code' => 'NU', 'name' => 'Iqaluit', 'latitude' => 63.7466930, 'longitude' => -68.5170000],
            ['country_id' => 38, 'state_code' => 'ON', 'name' => 'Toronto', 'latitude' => 43.6532250, 'longitude' => -79.3831860],
            ['country_id' => 38, 'state_code' => 'PE', 'name' => 'Charlottetown', 'latitude' => 46.2382400, 'longitude' => -63.1310730],
            ['country_id' => 38, 'state_code' => 'QC', 'name' => 'Montreal', 'latitude' => 45.5016890, 'longitude' => -73.5672560],
            ['country_id' => 38, 'state_code' => 'SK', 'name' => 'Saskatoon', 'latitude' => 52.1332130, 'longitude' => -106.6700440],
            ['country_id' => 38, 'state_code' => 'YT', 'name' => 'Whitehorse', 'latitude' => 60.7211880, 'longitude' => -135.0568390],
        ];

        $payload = [];
        foreach ($cities as $city) {
            $originalCode = $city['state_code'];
            $code = strtoupper($originalCode);
            $state = $statesByCode->get($code);

            if (! $state) {
                throw new RuntimeException("State code '{$originalCode}' not found. Run the StateSeeder first.");
            }

            $payload[] = [
                'state_id' => $state->id,
                'state_code' => $code,
                'country_id' => $city['country_id'],
                'country_code' => $state->country_code,
                'name' => $city['name'],
                'latitude' => $city['latitude'],
                'longitude' => $city['longitude'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('cities')->insert($payload);
    }
}
