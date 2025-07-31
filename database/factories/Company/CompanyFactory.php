<?php

declare(strict_types=1);

namespace Database\Factories\Company;

use App\Models\Address\Address;
use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'vat' => $this->faker->optional(0.7)->bothify('DE#########'),
            'email' => $this->faker->optional(0.8)->companyEmail(),
            'public_email' => $this->faker->optional(0.6)->companyEmail(),
            'public_phone' => $this->faker->optional(0.7)->phoneNumber(),
            'industry' => $this->faker->optional(0.8)->randomElement([
                'IT & Software',
                'Marketing & Sales',
                'Healthcare',
                'Finance',
                'Education',
                'Retail',
                'Manufacturing',
                'Hospitality',
                'Construction',
                'Transportation',
            ]),
            'provider' => $this->faker->optional(0.3)->randomElement([
                'stepstone',
                'xing',
                'linkedin',
            ]),
            'logo' => $this->faker->optional(0.4)->imageUrl(200, 200, 'business'),
            'address_id' => null, // Will be set by relationship factories if needed
        ];
    }

    public function withAddress(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'address_id' => Address::factory()->withGermanData(),
            ];
        });
    }

    public function fromProvider(string $provider): static
    {
        return $this->state(function (array $attributes) use ($provider) {
            return [
                'provider' => $provider,
            ];
        });
    }

    public function native(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'provider' => null,
            ];
        });
    }
}
