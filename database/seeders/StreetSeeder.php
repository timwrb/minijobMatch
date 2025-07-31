<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address\City;
use App\Models\Address\Street;
use Illuminate\Database\Seeder;

class StreetSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have cities first
        if (City::count() === 0) {
            $this->call(CitySeeder::class);
        }

        $cities = City::all();
        $streetNames = [
            'Hauptstraße',
            'Bahnhofstraße',
            'Kirchstraße',
            'Poststraße',
            'Marktplatz',
            'Schulstraße',
            'Gartenstraße',
            'Dorfstraße',
            'Lindenstraße',
            'Berliner Straße',
        ];

        foreach ($cities as $city) {
            // Create 2-3 streets per city
            $numberOfStreets = rand(2, 3);
            for ($i = 0; $i < $numberOfStreets; $i++) {
                Street::create([
                    'name' => $streetNames[array_rand($streetNames)],
                    'city_id' => $city->id,
                ]);
            }
        }
    }
}
