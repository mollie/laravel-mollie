<?php

declare(strict_types=1);

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteManager;

class MollieSocialiteServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return interface_exists(Factory::class)
            ? [SocialiteManager::class]
            : [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving(
            SocialiteManager::class,
            function (SocialiteManager $socialite) {
                $socialite->extend('mollie', function (Container $app) use ($socialite) {
                    $config = $app['config']['services.mollie'];

                    return $socialite->buildProvider(MollieConnectProvider::class, $config);
                });
            }
        );
    }
}
