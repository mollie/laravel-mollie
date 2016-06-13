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

    public function testMollieManagerIsInjectable()
    {
        $this->assertIsInjectable(MollieManager::class);
    }

    public  function testMollieApiWrapperIsInjectable()
    {
        $this->assertIsInjectable(MollieApiWrapper::class);
    }
    
    public function testMollieApiClientIsInjectable()
    {
        $this->assertIsInjectable(\Mollie_API_Client::class);
    }

}