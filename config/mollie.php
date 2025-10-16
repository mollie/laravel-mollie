<?php

declare(strict_types=1);

use Mollie\Laravel\EventWebhookDispatcher;
use Mollie\Laravel\Middleware\ValidatesWebhookSignatures;

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

        'prefix' => env('MOLLIE_WEBHOOKS_PREFIX', 'api'),

        'path' => env('MOLLIE_WEBHOOKS_PATH', 'mollie/webhooks'),

        'middleware' =>  [
            ValidatesWebhookSignatures::class,
        ],

        /**
         * The dispatcher to use for webhook events.
         *
         * Note: The dispatcher must implement the WebhookDispatcher interface.
         */
        'dispatcher' => EventWebhookDispatcher::class,

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
