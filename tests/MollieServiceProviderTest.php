<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Laravel\MollieServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;

class MollieServiceProviderTest extends TestCase
{
    #[Test]
    #[DataProvider('missingSigningSecrets')]
    public function it_rejects_missing_signing_secrets(string|array|null $secrets): void
    {
        config(['mollie.webhooks.signing_secrets' => $secrets]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No signing secrets for Mollie webhooks are set');

        resolve(SignatureValidator::class);
    }

    public static function missingSigningSecrets(): array
    {
        return [
            'unset' => [null],
            'empty string' => [''],
            'empty array' => [[]],
            'whitespace' => ['   '],
            'empty comma-separated entries' => [' , , '],
        ];
    }

    /**
     * Test that the service provider can be registered and booted without an API key.
     * This simulates the package installation scenario where the user hasn't configured a key yet.
     */
    #[Test]
    public function it_service_provider_installation_without_api_key()
    {
        // Clear the API key in the config
        config(['mollie.key' => '']);

        // Create a new instance of the service provider
        $provider = new MollieServiceProvider(app());

        // Register and boot should not throw exceptions
        $provider->register();
        $provider->boot();

        // Verify the service provider registered the MollieApiClient
        $this->assertTrue(app()->bound(MollieApiClient::class));

        // Resolving the client should not throw an exception
        $client = resolve(MollieApiClient::class);
        $this->assertInstanceOf(MollieApiClient::class, $client);

        // Verify no API key was set (authenticator should be null)
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('authenticator');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($client));
    }

    /**
     * Test that the service provider can be registered and booted with a valid API key.
     */
    #[Test]
    public function it_service_provider_with_valid_api_key()
    {
        // Set a valid API key in the config
        config(['mollie.key' => 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);

        // Create a new instance of the service provider
        $provider = new MollieServiceProvider($this->app);

        // Register and boot should not throw exceptions
        $provider->register();
        $provider->boot();

        // Verify the service provider registered the MollieApiClient
        $this->assertTrue($this->app->bound(MollieApiClient::class));

        // Resolving the client should not throw an exception
        $client = $this->app->make(MollieApiClient::class);
        $this->assertInstanceOf(MollieApiClient::class, $client);
    }
}
