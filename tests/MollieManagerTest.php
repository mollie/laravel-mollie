<?php

namespace Mollie\Laravel\Tests;

use Mollie\Laravel\MollieManager;
use Mollie\Laravel\Wrappers\MollieApiWrapper;

/**
 * Class MollieManagerTest
 */
class MollieManagerTest extends TestCase
{
    /**
     * MollieManager instance.
     *
     * @var MollieManager
     */
    protected $manager;

    /**
     * @before
     */
    public function setUpManagerInstance()
    {
        $this->manager = new MollieManager($this->app);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(MollieManager::class, $this->manager);
    }

    public function testApiMethod()
    {
        $this->assertInstanceOf(MollieApiWrapper::class, $this->manager->api());
        $this->assertSame($this->app['mollie.api'], $this->manager->api());
    }
}