<?php

namespace Mollie\Laravel\Tests;

use Mollie\Api\MollieApiClient;
use Mollie\Laravel\MollieLaravelHttpClientAdapter;
use ReflectionClass;

class MollieApiClientTest extends TestCase
{
    public function testInjectedHttpAdapterIsLaravelHttpClientAdapter()
    {
        $this->assertInstanceOf(
            MollieLaravelHttpClientAdapter::class,
            $this->getUnaccessiblePropertyValue('httpClient')
        );
    }

    public function testApiKeyIsSetOnResolvingApiClient()
    {
        config(['mollie.key' => 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);

        $this->assertEquals(
            'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz',
            $this->getUnaccessiblePropertyValue('apiKey')
        );
    }

    public function testDoesNotSetApiKeyIfKeyIsEmpty()
    {
        config(['mollie.key' => '']);

        $this->assertEquals(
            null,
            $this->getUnaccessiblePropertyValue('apiKey')
        );
    }

    private function getUnaccessiblePropertyValue(string $propertyName): mixed
    {
        $resolvedInstance = resolve(MollieApiClient::class);

        $reflection = new ReflectionClass($resolvedInstance);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($resolvedInstance);
    }
}
