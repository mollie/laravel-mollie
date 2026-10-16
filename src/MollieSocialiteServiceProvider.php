<?php

declare(strict_types=1);

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteManager;

class MollieSocialiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! interface_exists(Factory::class)) {
            return;
        }

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
