<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Laravel\Socialite\SocialiteServiceProvider;
use Mollie\Laravel\EventWebhookDispatcher;
use Mollie\Laravel\Middleware\ValidatesWebhookSignatures;
use Mollie\Laravel\MollieServiceProvider;
use Mollie\Laravel\MollieSocialiteServiceProvider;

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
        return [
            SocialiteServiceProvider::class,
            MollieServiceProvider::class,
            MollieSocialiteServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mollie.webhooks.enabled', true);
        $app['config']->set('mollie.webhooks.path', '/webhooks/mollie');
        $app['config']->set('mollie.webhooks.middleware', [ValidatesWebhookSignatures::class]);
        $app['config']->set('mollie.webhooks.dispatcher', EventWebhookDispatcher::class);
        $app['config']->set('mollie.webhooks.signing_secrets', 'test_secret');
    }
}
