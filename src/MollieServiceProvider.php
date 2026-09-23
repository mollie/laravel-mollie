<?php

declare(strict_types=1);

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Mollie\Api\Http\ExponentialRetryStrategy;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Laravel\Commands\SetupWebhookCommand;
use Mollie\Laravel\Contracts\WebhookDispatcher;
use RuntimeException;

class MollieServiceProvider extends ServiceProvider
{
    const PACKAGE_VERSION = '5.0.0';

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/webhook.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/mollie.php' => config_path('mollie.php')]);

            $this->commands([
                SetupWebhookCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/mollie.php', 'mollie'
        );

        $this->app->bind(
            MollieApiClient::class,
            function (Container $app) {
                $client = (new MollieApiClient(new MollieLaravelHttpClientAdapter))
                    ->addVersionString('MollieLaravel/' . self::PACKAGE_VERSION);

                if ($token = config('mollie.key')) {
                    $client->setToken($token);
                }

                $this->configureRetryStrategy($client);

                return $client;
            }
        );

        $this->app->singleton(SignatureValidator::class, function () {
            $signingSecrets = config('mollie.webhooks.signing_secrets');

            if (is_string($signingSecrets)) {
                $signingSecrets = array_values(array_filter(
                    array_map('trim', explode(',', $signingSecrets)),
                    fn (string $secret) => $secret !== ''
                ));
            }

            throw_if(
                ! $signingSecrets,
                new RuntimeException('No signing secrets for Mollie webhooks are set')
            );

            return new SignatureValidator($signingSecrets);
        });

        $this->app->bind(WebhookDispatcher::class, function (Container $app) {
            return $app->make(config('mollie.webhooks.dispatcher') ?? EventWebhookDispatcher::class);
        });
    }

    private function configureRetryStrategy(MollieApiClient $client): void
    {
        $strategy = strtolower((string) config('mollie.retry.strategy', 'linear'));
        $maxRetries = (int) config('mollie.retry.max_retries', 5);
        $delayMs = (int) config('mollie.retry.delay_ms', 1000);

        if ($strategy === 'linear') {
            $client->setRetryStrategy(new LinearRetryStrategy(
                maxRetries: $maxRetries,
                delayIncreaseMs: $delayMs,
            ));

            return;
        }

        if ($strategy === 'exponential') {
            $client->setRetryStrategy(new ExponentialRetryStrategy(
                maxRetries: $maxRetries,
                baseDelayMs: $delayMs,
                multiplier: (float) config('mollie.retry.exponential.multiplier', 2.0),
                maxDelayMs: (int) config('mollie.retry.exponential.max_delay_ms', 30000),
                jitter: (bool) config('mollie.retry.exponential.jitter', true),
            ));
        }
    }
}
