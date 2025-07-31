<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have states and coordinates first
        if (State::count() === 0) {
            $this->call(StateSeeder::class);
        }

        if (GeoCoordinate::count() === 0) {
            $this->call(GeoCoordinateSeeder::class);
        }

        $germanCities = [
            ['name' => 'Berlin', 'zip' => '10115', 'state_iso' => 'BE'],
            ['name' => 'Hamburg', 'zip' => '20095', 'state_iso' => 'HH'],
            ['name' => 'München', 'zip' => '80331', 'state_iso' => 'BY'],
            ['name' => 'Köln', 'zip' => '50667', 'state_iso' => 'NW'],
            ['name' => 'Frankfurt am Main', 'zip' => '60311', 'state_iso' => 'HE'],
        ];

        $coordinates = GeoCoordinate::all();

        foreach ($germanCities as $index => $cityData) {
            $state = State::where('iso_code', $cityData['state_iso'])->first();
            $coordinate = $coordinates->get($index % $coordinates->count());

            if ($state && $coordinate) {
                City::create([
                    'name' => $cityData['name'],
                    'zip' => $cityData['zip'],
                    'state_id' => $state->id,
                    'geo_coordinate_id' => $coordinate->id,
                ]);
            }
        }
    }
}
