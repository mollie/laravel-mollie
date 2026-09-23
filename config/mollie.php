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
     * Retry configuration for API calls.
     *
     * The SDK uses a linear retry strategy by default. Set the strategy to
     * "exponential" to retry temporary network failures and HTTP 429 responses
     * with exponential backoff. A Retry-After delay above max_delay_ms is not retried.
     */
    'retry' => [
        'strategy' => env('MOLLIE_RETRY_STRATEGY', 'linear'),
        'max_retries' => env('MOLLIE_RETRY_MAX_RETRIES', 5),
        'delay_ms' => env('MOLLIE_RETRY_DELAY_MS', 1000),

        'exponential' => [
            'multiplier' => env('MOLLIE_RETRY_EXPONENTIAL_MULTIPLIER', 2.0),
            'max_delay_ms' => env('MOLLIE_RETRY_EXPONENTIAL_MAX_DELAY_MS', 30000),
            'jitter' => env('MOLLIE_RETRY_EXPONENTIAL_JITTER', true),
        ],
    ],

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
        'middleware' => [
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
    ],
];
