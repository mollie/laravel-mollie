<?php

namespace Mollie\Laravel\Tests;

use Mollie\Laravel\MollieServiceProvider;
use Mollie\Laravel\Tests\TempHelpers\LaravelTrait;

/**
 * This is the abstract test case class.
 */
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
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
