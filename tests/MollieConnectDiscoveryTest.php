<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Illuminate\Foundation\Application;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteServiceProvider;
use Mollie\Laravel\MollieConnectProvider;
use Mollie\Laravel\MollieSocialiteServiceProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Verifies the mollie socialite driver is available in a real application.
 *
 * MollieConnectTest registers MollieSocialiteServiceProvider through Testbench's
 * getPackageProviders(), which registers it eagerly and therefore never exercises
 * the path a real application takes. Package discovery instead honours
 * DeferrableProvider, so this test case leaves the provider out of the eager list
 * and registers it the way Laravel's ProviderRepository would.
 */
class MollieConnectDiscoveryTest extends TestCase
{
    /**
     * Only the providers a host application gets from laravel/socialite itself.
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SocialiteServiceProvider::class,
        ];
    }

    /**
     * Register the package provider exactly like Laravel's package discovery does:
     * deferred providers are only mapped by the services they say they provide,
     * everything else is registered immediately.
     */
    private function discoverSocialiteProvider(): void
    {
        $provider = new MollieSocialiteServiceProvider($this->app);

        if ($provider->isDeferred()) {
            $this->app->addDeferredServices(
                array_fill_keys($provider->provides(), MollieSocialiteServiceProvider::class)
            );

            return;
        }

        $this->app->register($provider);
    }

    #[Test]
    public function it_resolves_the_mollie_driver_when_registered_through_package_discovery()
    {
        config(['services.mollie' => [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'redirect' => 'test_redirect',
        ]]);

        $this->discoverSocialiteProvider();

        $this->assertInstanceOf(MollieConnectProvider::class, Socialite::with('mollie'));
    }
}
