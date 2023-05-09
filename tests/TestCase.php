<?php

namespace Mollie\Laravel\Tests;

use Mollie\Laravel\MollieServiceProvider;
use Mollie\Laravel\Tests\TempHelpers\LaravelTrait;

/**
 * This is the abstract test case class.
 */
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use LaravelTrait;

    /**
     * Get the service provider class.
     *
     * @return string
     */
    protected function getServiceProviderClass()
    {
        return MollieServiceProvider::class;
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        $providers = [MollieServiceProvider::class];

        if (interface_exists('Laravel\Socialite\Contracts\Factory')) {
            $providers[] = \Laravel\Socialite\SocialiteServiceProvider::class;
        }

        return $providers;
    }
}
