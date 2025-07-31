<?php

declare(strict_types=1);

namespace Tests\Feature\Stripe;

use Stripe\StripeClient;

describe('Stripe Live', function () {
    test('Default Subscription is set up correctly', function () {
        $client = new StripeClient(config('cashier.secret'));

        $default_price = config('services.stripe.live.default_subscription.price_id');
        $price = $client->prices->retrieve($default_price)->toArray();

        $meter_id = data_get($price, 'recurring.meter');
        expect($meter_id)->toBe(config('services.stripe.live.default_subscription.meter_id'));
        $meter = $client->billing->meters->retrieve($meter_id)->toArray();

        expect(data_get($meter, 'default_aggregation.formula'))->toBe('count')
            ->and(data_get($meter, 'value_settings.event_payload_key'))->toBe('value');
    })->skip();
})->todo();
