<?php

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class MollieSocialiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->extendSocialite();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // No registration needed for Socialite extension
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
}
