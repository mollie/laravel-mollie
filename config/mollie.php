<?php

declare(strict_types=1);

return [

    'key' => env('MOLLIE_KEY', 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),

    // If you intend on using Mollie Connect, place the following in the 'config/services.php'
    // 'mollie' => [
    //     'client_id'     => env('MOLLIE_CLIENT_ID', 'app_xxx'),
    //     'client_secret' => env('MOLLIE_CLIENT_SECRET'),
    //     'redirect'      => env('MOLLIE_REDIRECT_URI'),
    // ],

    'webhooks' => [
        'enabled' => env('MOLLIE_WEBHOOKS_ENABLED', false),

        /**
         * A comma separated list of signing secrets.
         */
        'signing_secrets' => env('MOLLIE_WEBHOOK_SIGNING_SECRETS'),

        /**
         * If true, legacy webhooks without a signature will be accepted.
         */
        'legacy_webhook_enabled' => env('MOLLIE_LEGACY_WEBHOOK_ENABLED', false),
    ],

];
