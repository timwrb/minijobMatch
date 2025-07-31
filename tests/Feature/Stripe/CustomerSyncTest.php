<?php

declare(strict_types=1);

namespace Tests\Feature\Stripe;

use App\Models\Company\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Tests\Concerns\InteractsWithStripeTestingSandbox;
use Throwable;

uses(RefreshDatabase::class, InteractsWithStripeTestingSandbox::class);

beforeEach(function () {
    $this->configureStripeTestingSandbox();
});

afterEach(/**
 * @throws ApiErrorException
 */ function () {
    $this->cleanupAllStripeCustomers();
});

describe('Companies are synced as Stripe Customers', function () {
    test(/**
     * @throws ApiErrorException
     * @throws Throwable
     */ 'creates a stripe customer correctly.', function () {

        $company = Company::factory()->native()->withAddress()->create();

        $stripeCustomer = $this->retrieveCustomer($company);
        $stripeData = $stripeCustomer->toArray();

        // Test Stripe customer was created correctly
        expect($stripeCustomer)->toBeInstanceOf(Customer::class)
            ->and($company->stripe_id)->toBe($stripeData['id'])
            ->and($stripeData['name'])->toBe($company->name)
            ->and($stripeData['email'])->toBe($company->email)
            ->and($stripeData['phone'])->toBe($company->public_phone)
            ->and($stripeData['metadata']['company_id'])->toBe((string) $company->id)
            ->and($stripeData['metadata']['application_deleted'])->toBe('false');

        // Test address data
        $expectedAddress = $company->stripeAddress();
        expect($stripeData['address']['city'])->toBe($expectedAddress['city'])
            ->and($stripeData['address']['country'])->toBe($expectedAddress['country'])
            ->and($stripeData['address']['line1'])->toBe($expectedAddress['line1'])
            ->and($stripeData['address']['line2'])->toBe($expectedAddress['line2'] ?? '')
            ->and($stripeData['address']['postal_code'])->toBe($expectedAddress['postal_code'])
            ->and($stripeData['address']['state'])->toBe($expectedAddress['state']);
    });

    test(/**
     * @throws ApiErrorException
     * @throws Throwable
     */ 'updates stripe customer when company data changes', function () {

        $company = Company::factory()->native()->withAddress()->create();

        // Verify initial creation
        $stripeCustomer = $this->retrieveCustomer($company);
        expect($stripeCustomer)->toBeInstanceOf(Customer::class)
            ->and($company->stripe_id)->toBe($stripeCustomer->id);

        // Update company data
        $company->update([
            'name' => 'Updated Company Name',
            'email' => 'updated@company.com',
            'public_phone' => '+49 30 12345678',
        ]);

        // Retrieve updated Stripe customer
        $updatedStripeCustomer = $this->retrieveCustomer($company);
        $updatedData = $updatedStripeCustomer->toArray();

        // Verify updates were synced
        expect($updatedData['name'])->toBe('Updated Company Name')
            ->and($updatedData['email'])->toBe('updated@company.com')
            ->and($updatedData['phone'])->toBe('+49 30 12345678')
            ->and($updatedData['metadata']['company_id'])->toBe((string) $company->id)
            ->and($updatedData['metadata']['application_deleted'])->toBe('false');
    });

    test(/**
     * @throws ApiErrorException
     * @throws Throwable
     */ 'does not create stripe customer for companies from external providers', function () {

        $company = Company::factory()->fromProvider('stepstone')->withAddress()->create();

        // Verify no Stripe customer was created
        expect($company->stripe_id)->toBeNull()
            ->and($company->shouldSyncWithStripe())->toBeFalse()
            ->and($company->provider)->toBe('stepstone');

        // No cleanup needed as no Stripe customer was created
    });

    test(/**
     * @throws ApiErrorException
     * @throws Throwable
     */ 'marks stripe customer as deleted when company is deleted from application', function () {

        $company = Company::factory()->native()->withAddress()->create();

        // Verify initial creation
        $stripeCustomer = $this->retrieveCustomer($company);
        expect($stripeCustomer)->toBeInstanceOf(Customer::class)
            ->and($company->stripe_id)->toBe($stripeCustomer->id);

        $stripeId = $company->stripe_id;

        // Delete the company (this should trigger the deleting event)
        $company->delete();

        // Retrieve Stripe customer and check metadata
        $stripeClient = $this->stripeClient();
        $deletedStripeCustomer = $stripeClient->customers->retrieve($stripeId, []);
        $deletedData = $deletedStripeCustomer->toArray();

        expect($deletedData['metadata']['application_deleted'])->toBe('true')
            ->and($deletedData['metadata']['company_id'])->toBe((string) $company->id);
    });

    test(/**
     * @throws ApiErrorException
     * @throws Throwable
     */ 'handles stripe sync errors gracefully', function () {

        // Create company with invalid email to trigger potential Stripe error
        $company = Company::factory()->native()->withAddress()->make([
            'email' => null, // This might cause issues but should be handled gracefully
        ]);

        $company->save();

        // Even if there's an error, the company should still be created locally
        expect($company->exists)->toBeTrue()
            ->and($company->shouldSyncWithStripe())->toBeTrue();

        // No manual cleanup needed - afterEach hook handles it
    });

    test(/**
     * @throws ApiErrorException
     * @throws Throwable
     */ 'syncs company without address correctly', function () {

        $company = Company::factory()->native()->create(); // No address

        $stripeCustomer = $this->retrieveCustomer($company);
        $stripeData = $stripeCustomer->toArray();

        // Verify customer created with basic data
        expect($stripeCustomer)->toBeInstanceOf(Customer::class)
            ->and($company->stripe_id)->toBe($stripeData['id'])
            ->and($stripeData['name'])->toBe($company->name)
            ->and($stripeData['email'])->toBe($company->email)
            ->and($stripeData['metadata']['company_id'])->toBe((string) $company->id);

        // Address should be empty array in local data, null or empty in Stripe
        $expectedAddress = $company->stripeAddress();
        expect($expectedAddress)->toBe([]);
    });
})->group('stripe');
