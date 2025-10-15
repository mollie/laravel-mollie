<?php

declare(strict_types=1);

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Webhooks\SignatureValidator;
use RuntimeException;
use Mollie\Laravel\Contracts\WebhookDispatcher;
use Mollie\Laravel\Commands\RevealWebhookPathCommand;
use Mollie\Laravel\Commands\SetupWebhookCommand;

class MollieServiceProvider extends ServiceProvider
{
    const PACKAGE_VERSION = '4.0.0';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/mollie.php' => config_path('mollie.php')]);

            $this->commands([
                SetupWebhookCommand::class,
                RevealWebhookPathCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            SetupWebhookCommand::class,
            RevealWebhookPathCommand::class,
            MollieApiClient::class,
            MollieManager::class,
            WebhookDispatcher::class,
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/mollie.php', 'mollie'
        );

        $this->app->singleton(
            MollieApiClient::class,
            function (Container $app) {
                $client = (new MollieApiClient(new MollieLaravelHttpClientAdapter))
                    ->addVersionString('MollieLaravel/' . self::PACKAGE_VERSION);

                if (! empty($apiKey = $app['config']['mollie.key'])) {
                    $client->setApiKey($apiKey);
                }

                return $client;
            }
        );

        $this->app->singleton(MollieManager::class);

        $this->app->bind(SignatureValidator::class, function (Container $app) {
            throw_if(
                config('mollie.webhooks.enabled') && ! config('mollie.webhooks.signing_secrets'),
                new RuntimeException('Webhooks are enabled but no signing secrets are set')
            );

            return new SignatureValidator(config('mollie.webhooks.signing_secrets'));
        });

        $this->app->bind(WebhookDispatcher::class, function (Container $app) {
            return $app->make(config('mollie.webhooks.dispatcher'));
        });
    }
}
