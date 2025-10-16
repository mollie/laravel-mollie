<?php

declare(strict_types=1);

namespace Mollie\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Fake\MockMollieClient;

/**
 * (Facade) Class Mollie.
 *
 * @method static void assertSent(string $class)
 * @method static void assertSentCount(int $count)
 *
 * @see \Mollie\Api\Fake\MockMollieClient
 */
class Mollie extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MollieApiClient::class;
    }

    public static function fake(array $expectedResponses = []): MockMollieClient
    {
        return tap(new MockMollieClient($expectedResponses), function ($fake) {
            static::swap($fake);
        });
    }

    public static function api()
    {
        return static::getFacadeRoot();
    }
}
