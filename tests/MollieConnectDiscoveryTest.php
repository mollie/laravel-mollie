<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\ProviderRepository;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteServiceProvider;
use Mollie\Laravel\MollieConnectProvider;
use Mollie\Laravel\MollieServiceProvider;
use Mollie\Laravel\MollieSocialiteServiceProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Verifies the Mollie driver is available through Laravel's provider manifest.
 */
class MollieConnectDiscoveryTest extends TestCase
{
    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [];
    }

    #[Test]
    public function it_resolves_the_mollie_driver_when_registered_through_package_discovery()
    {
        $this->assertFalse($this->app->providerIsLoaded(MollieSocialiteServiceProvider::class));

        config(['services.mollie' => [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'redirect' => 'test_redirect',
        ]]);

        $manifest = sys_get_temp_dir() . '/mollie-provider-' . bin2hex(random_bytes(8)) . '.php';

        try {
            (new ProviderRepository($this->app, new Filesystem, $manifest))->load([
                SocialiteServiceProvider::class,
                MollieServiceProvider::class,
                MollieSocialiteServiceProvider::class,
            ]);

            $this->assertSame(SocialiteServiceProvider::class, $this->app->getDeferredServices()[Factory::class]);
            $this->assertInstanceOf(MollieConnectProvider::class, Socialite::with('mollie'));
        } finally {
            @unlink($manifest);
        }
    }
}
