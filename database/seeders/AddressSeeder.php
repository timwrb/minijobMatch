<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address\Address;
use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\Street;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have streets first
        if (Street::count() === 0) {
            $this->call(StreetSeeder::class);
        }

        $streets = Street::all();
        $cities = City::all();
        $coordinates = GeoCoordinate::all();

        // Create complete addresses with street details
        foreach ($streets as $street) {
            // Create 3-5 addresses per street
            $numberOfAddresses = rand(3, 5);
            for ($i = 0; $i < $numberOfAddresses; $i++) {
                Address::create([
                    'country_iso_code' => 'DE',
                    'house_number' => (string) rand(1, 100),
                    'address_addition' => rand(1, 10) <= 3 ? 'Apt '.rand(1, 20) : null,
                    'street_id' => $street->id,
                    'city_id' => $street->city_id,
                    'geo_coordinate_id' => $coordinates->isNotEmpty() && rand(1, 10) <= 8
                        ? $coordinates->random()->id
                        : null,
                ]);
            }
        }

        // Create city-only addresses (vacancy provider scenario)
        foreach ($cities as $city) {
            // Create 2-3 city-only addresses per city
            $numberOfCityAddresses = rand(2, 3);
            for ($i = 0; $i < $numberOfCityAddresses; $i++) {
                Address::create([
                    'country_iso_code' => 'DE',
                    'house_number' => null,
                    'address_addition' => null,
                    'street_id' => null,
                    'city_id' => $city->id,
                    'geo_coordinate_id' => $coordinates->isNotEmpty() && rand(1, 10) <= 5
                        ? $coordinates->random()->id
                        : null,
                ]);
            }
        }
    }
}
