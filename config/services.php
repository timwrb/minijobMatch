<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'live' => [
            'default_subscription' => [
                'price_id' => env('DEFAULT_SUBSCRIPTION_PRICE_ID'),
                'meter_id' => env('DEFAULT_SUBSCRIPTION_METER_ID'),
                'sub_id' => env('DEFAULT_SUBSCRIPTION_ID'),
                'event_name' => env('DEFAULT_SUBSCRIPTION_EVENT_NAME'),
            ],
        ],
        'testing' => [
            'testing_key' => env('TESTING_STRIPE_KEY'),
            'testing_secret' => env('TESTING_STRIPE_SECRET'),
            'default_subscription' => [
                'price_id' => env('TESTING_DEFAULT_SUBSCRIPTION_PRICE_ID'),
                'meter_id' => env('TESTING_DEFAULT_SUBSCRIPTION_METER_ID'),
                'sub_id' => env('TESTING_DEFAULT_SUBSCRIPTION_ID'),
                'event_name' => env('TESTING_DEFAULT_SUBSCRIPTION_EVENT_NAME'),
            ],
        ],
    ],

];
