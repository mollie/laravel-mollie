<?php

namespace Mollie\Laravel\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Mollie\Laravel\MollieServiceProvider;

/**
 * This is the abstract test case class.
 */
abstract class TestCase extends AbstractPackageTestCase
{
    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return MollieServiceProvider::class;
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
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
