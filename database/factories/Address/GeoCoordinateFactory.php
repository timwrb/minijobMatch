<?php

declare(strict_types=1);

namespace Database\Factories\Address;

use App\Models\Address\GeoCoordinate;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeoCoordinateFactory extends Factory
{
    protected $model = GeoCoordinate::class;

    public function definition(): array
    {
        return [
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }

    public function germany(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => $this->faker->latitude(47.2, 55.1), // Germany bounds
            'longitude' => $this->faker->longitude(5.9, 15.0),
        ]);
    }
}
