<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for Stripe payment gateway.
    |
    */

    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for Stripe transactions
    |
    */
    'currency' => env('STRIPE_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Test Mode
    |--------------------------------------------------------------------------
    |
    | Set to true when using test keys, false for live keys
    |
    */
    'test_mode' => env('STRIPE_TEST_MODE', true),
];
