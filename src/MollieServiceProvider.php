<?php

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Mollie\Api\MollieApiClient;

class MollieServiceProvider extends ServiceProvider
{
    const PACKAGE_VERSION = '3.0.0';

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
        $this->extendSocialite();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../config/mollie.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([$source => config_path('mollie.php')]);
        }

        $this->mergeConfigFrom($source, 'mollie');
    }

    /**
     * Extend the Laravel Socialite factory class, if available.
     *
     * @return void
     */
    protected function extendSocialite()
    {
        if (interface_exists($socialiteFactoryClass = \Laravel\Socialite\Contracts\Factory::class)) {
            $socialite = $this->app->make($socialiteFactoryClass);

            $socialite->extend('mollie', function (Container $app) use ($socialite) {
                $config = $app['config']['services.mollie'];

                return $socialite->buildProvider(MollieConnectProvider::class, $config);
            });
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            MollieApiClient::class,
            function (Container $app) {
                $client = (new MollieApiClient(new MollieLaravelHttpClientAdapter))
                    ->addVersionString('MollieLaravel/' . self::PACKAGE_VERSION);

                if (!empty($apiKey = $app['config']['mollie.key'])) {
                    $client->setApiKey($apiKey);
                }

                return $client;
            }
        );
    }
}
