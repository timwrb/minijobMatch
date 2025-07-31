<?php

declare(strict_types=1);

namespace Database\Factories\Address;

use App\Models\Address\Address;
use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\Street;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'country_iso_code' => 'DE',
            'house_number' => $this->faker->optional(0.7)->buildingNumber(),
            'address_addition' => $this->faker->optional(0.3)->randomElement(['Apt A', 'Apt B', 'Apt 1', 'Apt 2', 'Suite 1', 'Suite 2']),
            'street_id' => $this->faker->optional(0.7)->randomElement([Street::factory(), null]),
            'city_id' => City::factory(),
            'geo_coordinate_id' => $this->faker->optional(0.8)->randomElement([
                GeoCoordinate::factory(),
                null,
            ]),
        ];
    }

    public function withGermanData(): static
    {
        return $this->state(fn (array $attributes) => [
            'country_iso_code' => 'DE',
            'street_id' => Street::factory()->germanStreets(),
            'city_id' => City::factory()->withGermanState(),
            'geo_coordinate_id' => GeoCoordinate::factory()->germany(),
        ]);
    }

    public function cityOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'house_number' => null,
            'address_addition' => null,
            'street_id' => null,
            'city_id' => City::factory()->withGermanState(),
        ]);
    }

    public function withoutGeoCoordinate(): static
    {
        return $this->state(fn (array $attributes) => [
            'geo_coordinate_id' => null,
        ]);
    }
}
