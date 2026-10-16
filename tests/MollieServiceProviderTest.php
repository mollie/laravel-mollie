<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Mollie\Api\Http\ExponentialRetryStrategy;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Laravel\MollieServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
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
        $reflection = new ReflectionClass($client);
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

    #[Test]
    public function it_keeps_the_default_linear_retry_strategy()
    {
        config([
            'mollie.retry.strategy' => 'linear',
            'mollie.retry.max_retries' => 2,
            'mollie.retry.delay_ms' => 500,
        ]);

        $strategy = $this->retryStrategy($this->app->make(MollieApiClient::class));

        $this->assertInstanceOf(LinearRetryStrategy::class, $strategy);
        $this->assertSame(2, $strategy->maxRetries());
        $this->assertSame(500, $strategy->delayBeforeAttemptMs(1));
        $this->assertSame(1000, $strategy->delayBeforeAttemptMs(2));
    }

    #[Test]
    public function it_configures_exponential_retry_strategy_when_selected()
    {
        config([
            'mollie.retry.strategy' => 'exponential',
            'mollie.retry.max_retries' => 4,
            'mollie.retry.delay_ms' => 250,
            'mollie.retry.exponential.multiplier' => 3.0,
            'mollie.retry.exponential.max_delay_ms' => 10000,
            'mollie.retry.exponential.jitter' => false,
        ]);

        $strategy = $this->retryStrategy($this->app->make(MollieApiClient::class));

        $this->assertInstanceOf(ExponentialRetryStrategy::class, $strategy);
        $this->assertSame(4, $strategy->maxRetries());
        $this->assertSame(250, $strategy->delayBeforeAttemptMs(1));
        $this->assertSame(750, $strategy->delayBeforeAttemptMs(2));
    }

    private function retryStrategy(MollieApiClient $client): mixed
    {
        $reflection = new ReflectionClass($client);
        $property = $reflection->getProperty('retryStrategy');
        $property->setAccessible(true);

        return $property->getValue($client);
    }
}
