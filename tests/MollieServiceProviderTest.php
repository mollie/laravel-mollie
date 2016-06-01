<?php

namespace Mollie\Laravel\Tests;

use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Mollie\Laravel\MollieManager;
use Mollie\Laravel\Wrappers\MollieApiWrapper;

/**
 * This is the service provider test class.
 */
class MollieServiceProviderTest extends TestCase
{
    use ServiceProviderTrait;

    /**
     * @before
     */
    public function setUpEnv()
    {
        $this->app->config->set('app.env', 'local');
    }

    public function testMollieManagerIsInjectable()
    {
        $this->assertIsInjectable(MollieManager::class);
    }

    public  function testMollieApWrapperiIsInjectable()
    {
        $this->assertIsInjectable(MollieApiWrapper::class);
    }

}