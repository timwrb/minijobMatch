<?php

declare(strict_types=1);

namespace Tests\Feature\Stripe;

use App\Models\Company\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Exception\ApiErrorException;
use Tests\Concerns\InteractsWithStripeTestingSandbox;

uses(RefreshDatabase::class, InteractsWithStripeTestingSandbox::class);

beforeEach(function () {
    $this->configureStripeTestingSandbox();
});

afterEach(function () {
    $this->cleanupAllStripeCustomers();
});

describe('Example usage of InteractsWithStripeTestingSandbox trait', function () {
    test(/**
     * @throws ApiErrorException
     */ 'demonstrates trait usage', function () {

        $company = Company::factory()->native()->create();

        // Use trait methods
        $customer = $this->retrieveCustomer($company);
        $allCustomers = $this->getAllStripeCustomers();
        $customerCount = $this->countStripeCustomers();

        expect($customer)->not()->toBeNull()
            ->and($customerCount)->toBe(1)
            ->and($allCustomers)->toHaveCount(1);

        // Cleanup happens automatically in afterEach
    });
})->group('stripe', 'example');
