<?php

declare(strict_types=1);

use Mollie\Laravel\EventWebhookDispatcher;
use Mollie\Laravel\Middleware\ValidatesWebhookSignatures;

return [
    /**
     * API Key or Access Token to authenticate with the Mollie API.
     */
    'key' => env('MOLLIE_KEY', 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),

    /**
     * Webhooks configuration.
     */
    'webhooks' => [
        /**
         * If true, the webhook route will be registered.
         */
        'enabled' => env('MOLLIE_WEBHOOKS_ENABLED', false),

        /**
         * The path to use for incoming webhook requests.
         */
        'path' => env('MOLLIE_WEBHOOKS_PATH', '/webhooks/mollie'),

        /**
         * The middleware to use for incoming webhook requests.
         */
        'middleware' =>  [
            ValidatesWebhookSignatures::class,
        ],

        /**
         * The dispatcher determines how webhook events are treated by the app.
         * By default, events are dispatched as Laravel events. You can then listen to
         * these events via Subscriber or Listeners and react accordingly. Or you may implement
         * your own dispatcher to handle the events in a different way.
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
