<?php

namespace Mollie\Laravel\Tests;

use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\MollieApiClient;
use Mollie\Laravel\MollieLaravelHttpClientAdapter;
use ReflectionClass;

class MollieApiClientTest extends TestCase
{
    public function test_injected_http_adapter_is_laravel_http_client_adapter()
    {
        $this->assertInstanceOf(
            MollieLaravelHttpClientAdapter::class,
            $this->getUnaccessiblePropertyValue('httpClient')
        );
    }

    public function test_api_key_is_set_on_resolving_api_client()
    {
        config(['mollie.key' => 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);
        
        $client = resolve(MollieApiClient::class);
        $authenticator = $client->getAuthenticator();
        
        $this->assertInstanceOf(ApiKeyAuthenticator::class, $authenticator);
        $this->assertEquals(
            'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz',
            $this->getApiKeyFromAuthenticator($authenticator)
        );
    }

    public function test_does_not_set_api_key_if_key_is_empty()
    {
        config(['mollie.key' => '']);
        
        $client = resolve(MollieApiClient::class);
        $authenticator = $client->getAuthenticator();
        
        $this->assertNull($authenticator);
    }

    private function getUnaccessiblePropertyValue(string $propertyName): mixed
    {
        $resolvedInstance = resolve(MollieApiClient::class);

        $reflection = new ReflectionClass($resolvedInstance);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($resolvedInstance);
    }

    private function getApiKeyFromAuthenticator(object $authenticator): ?string
    {
        $reflection = new ReflectionClass($authenticator);
        $property = $reflection->getProperty('token');
        $property->setAccessible(true);
        
        return $property->getValue($authenticator);
    }
}
