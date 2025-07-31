<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\Company\Company;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

trait InteractsWithStripeTestingSandbox
{
    protected function configureStripeTestingSandbox(): void
    {
        // Override Cashier config to use testing Stripe keys from environment
        config([
            'cashier.key' => config('services.stripe.testing.testing_key', 'pk_test_fallback'),
            'cashier.secret' => config('services.stripe.testing.testing_secret', 'sk_test_fallback'),
            'queue.default' => 'sync', // Force synchronous queue processing for tests
        ]);
    }

    protected function stripeClient(): StripeClient
    {
        return new StripeClient(config('cashier.secret'));
    }

    /**
     * @throws ApiErrorException
     */
    protected function retrieveCustomer(Company $company): ?Customer
    {
        return $this->stripeClient()->customers->retrieve($company->stripe_id, []);
    }

    /**
     * @throws ApiErrorException
     */
    protected function cleanupAllStripeCustomers(): void
    {
        $stripeClient = $this->stripeClient();

        // List all customers in the testing sandbox
        $customers = $stripeClient->customers->all(['limit' => 100]);

        // Delete each customer
        foreach ($customers->data as $customer) {
            try {
                $stripeClient->customers->delete($customer->id, []);
            } catch (ApiErrorException $e) {
                // Log error but don't fail the test cleanup
                echo "Failed to delete customer {$customer->id}: ".$e->getMessage()."\n";
            }
        }
    }

    /**
     * @throws ApiErrorException
     */
    protected function deleteStripeCustomer(string $customerId): bool
    {
        $stripeClient = $this->stripeClient();
        $deleted = $stripeClient->customers->delete($customerId, []);

        return data_get($deleted, 'deleted') === true && data_get($deleted, 'id') === $customerId;
    }

    /**
     * Get all customers from Stripe testing sandbox
     *
     * @throws ApiErrorException
     */
    protected function getAllStripeCustomers(): array
    {
        return $this->stripeClient()->customers->all(['limit' => 100])->data;
    }

    /**
     * Count customers in Stripe testing sandbox
     *
     * @throws ApiErrorException
     */
    protected function countStripeCustomers(): int
    {
        return count($this->getAllStripeCustomers());
    }
}
