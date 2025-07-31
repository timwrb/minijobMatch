<?php

declare(strict_types=1);

namespace Database\Factories\Address;

use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'zip' => $this->faker->postcode(),
            'state_id' => State::inRandomOrder()->first()?->id ?? 1,
            'geo_coordinate_id' => GeoCoordinate::factory(),
        ];
    }

    public function withGermanState(): static
    {
        return $this->state(fn (array $attributes) => [
            'state_id' => State::where('country_iso_code', 'DE')->inRandomOrder()->first()?->id ?? 1,
            'geo_coordinate_id' => GeoCoordinate::factory()->germany(),
            'zip' => $this->faker->numerify('#####'),
        ]);
    }
}
