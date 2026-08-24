<?php

declare(strict_types=1);

namespace Mollie\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\MollieApiClient;

/**
 * (Facade) Class Mollie.
 *
 * @method static void assertSent(callable|string $callback)
 * @method static void assertSentCount(int $count)
 *
 * @see MockMollieClient
 */
class Mollie extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MollieApiClient::class;
    }

    public static function fake(array $expectedResponses = []): MockMollieClient
    {
        return tap(new MockMollieClient($expectedResponses), function ($mockClient) {
            static::swap($mockClient);
        });
    }

    /**
     * @deprecated Use the facade directly instead. Will be removed in a future major version.
     */
    public static function api(): MollieApiClient
    {
        return static::getFacadeRoot();
    }
}
