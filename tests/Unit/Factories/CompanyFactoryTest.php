<?php

declare(strict_types=1);

use App\Models\Address\Address;
use App\Models\Company\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('company factory creates valid company', function () {
    $company = Company::factory()->create();

    expect($company)->toBeInstanceOf(Company::class)
        ->and($company->name)->not->toBeNull()
        ->and($company->vat === null || is_string($company->vat))->toBeTrue()
        ->and($company->email === null || is_string($company->email))->toBeTrue()
        ->and($company->public_email === null || is_string($company->public_email))->toBeTrue()
        ->and($company->public_phone === null || is_string($company->public_phone))->toBeTrue()
        ->and($company->industry === null || is_string($company->industry))->toBeTrue()
        ->and($company->provider === null || is_string($company->provider))->toBeTrue()
        ->and($company->logo === null || is_string($company->logo))->toBeTrue()
        ->and($company->address_id)->toBeNull();
});

test('company factory can create multiple companies', function () {
    $companies = Company::factory()->count(3)->create();

    expect($companies)->toHaveCount(3)
        ->and($companies->first())->toBeInstanceOf(Company::class);
});

test('company factory can create company with address', function () {
    $company = Company::factory()->withAddress()->create();

    expect($company->address_id)->not->toBeNull()
        ->and($company->address)->toBeInstanceOf(Address::class);
});

test('company factory can create company from specific provider', function () {
    $company = Company::factory()->fromProvider('stepstone')->create();

    expect($company->provider)->toBe('stepstone');
});

test('company factory can create native company', function () {
    $company = Company::factory()->native()->create();

    expect($company->provider)->toBeNull();
});

test('company has proper cashier columns', function () {
    $company = Company::factory()->fromProvider('stepstone')->create();

    if ($company instanceof Company) {
        expect($company->stripe_id)->toBeNull()
            ->and($company->pm_type)->toBeNull()
            ->and($company->pm_last_four)->toBeNull()
            ->and($company->trial_ends_at)->toBeNull();
    }
});

test('company model uses billable trait', function () {
    $company = Company::factory()->create();

    expect(method_exists($company, 'createAsStripeCustomer'))->toBeTrue()
        ->and(method_exists($company, 'subscription'))->toBeTrue();
});
