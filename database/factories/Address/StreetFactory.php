<?php

declare(strict_types=1);

namespace Database\Factories\Address;

use App\Models\Address\City;
use App\Models\Address\Street;
use Illuminate\Database\Eloquent\Factories\Factory;

class StreetFactory extends Factory
{
    protected $model = Street::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName(),
            'city_id' => City::factory(),
        ];
    }

    public function germanStreets(): static
    {
        $germanStreetNames = [
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

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($germanStreetNames),
            'city_id' => City::factory()->withGermanState(),
        ]);
    }
}
