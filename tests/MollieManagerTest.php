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

    public function testConstructor()
    {
        $this->setUpManagerInstance();
        $this->assertInstanceOf(MollieManager::class, $this->manager);
    }

    public function testApiMethod()
    {
        $this->setUpManagerInstance();
        $this->assertInstanceOf(MollieApiWrapper::class, $this->manager->api());
        $this->assertSame($this->app['mollie.api'], $this->manager->api());
    }

    public function setUpManagerInstance()
    {
        $this->manager = new MollieManager($this->app);
    }
}
