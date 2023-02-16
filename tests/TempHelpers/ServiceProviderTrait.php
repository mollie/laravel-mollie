<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\TempHelpers;

use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Assert;
use ReflectionClass;

trait ServiceProviderTrait
{
    /**
     * Get the service provider class.
     *
     * @return string
     */
    abstract protected function getServiceProviderClass();

    public function testIsAServiceProvider()
    {
        $class = $this->getServiceProviderClass($this->app);

        $reflection = new ReflectionClass($class);

        $provider = new ReflectionClass(ServiceProvider::class);

        $msg = "Expected class '$class' to be a service provider.";

        $this->assertTrue($reflection->isSubclassOf($provider), $msg);
    }

    public function testProvides()
    {
        $class = $this->getServiceProviderClass($this->app);
        $reflection = new ReflectionClass($class);

        $method = $reflection->getMethod('provides');
        $method->setAccessible(true);

        $msg = "Expected class '$class' to provide a valid list of services.";

        if (is_callable([Assert::class, 'assertIsArray'])) {
            $this->assertIsArray($method->invoke(new $class($this->app)), $msg);
        } else {
            $this->assertInternalType('array', $method->invoke(new $class($this->app)), $msg);
        }
    }
}
