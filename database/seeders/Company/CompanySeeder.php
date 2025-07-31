<?php

declare(strict_types=1);

namespace Database\Seeders\Company;

use App\Models\Company\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Create some companies with addresses
        Company::factory()
            ->count(10)
            ->withAddress()
            ->create();

        // Create some companies from external providers
        Company::factory()
            ->count(5)
            ->fromProvider('stepstone')
            ->create();

        // Create some native companies (user-created)
        Company::factory()
            ->count(8)
            ->native()
            ->withAddress()
            ->create();
    }
}
