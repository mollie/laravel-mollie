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
}