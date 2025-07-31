<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address\GeoCoordinate;
use Illuminate\Database\Seeder;

class GeoCoordinateSeeder extends Seeder
{
    public function run(): void
    {
        // Create some sample geo coordinates for major German cities
        $coordinates = [
            ['latitude' => 52.5200, 'longitude' => 13.4050], // Berlin
            ['latitude' => 53.5511, 'longitude' => 9.9937],  // Hamburg
            ['latitude' => 48.1351, 'longitude' => 11.5820], // Munich
            ['latitude' => 50.9375, 'longitude' => 6.9603],  // Cologne
            ['latitude' => 50.1109, 'longitude' => 8.6821],  // Frankfurt
        ];

        foreach ($coordinates as $coordinate) {
            GeoCoordinate::create($coordinate);
        }
    }
}
