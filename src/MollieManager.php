<?php

declare(strict_types=1);

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Fake\MockMollieClient;

class MollieManager
{
    public function __construct(private Container $app) {}

    public function api(): MollieApiClient
    {
        return $this->app->make(MollieApiClient::class);
    }

    public function fake(array $expectedResponses = []): MockMollieClient
    {
        return tap(MollieApiClient::fake($expectedResponses), function ($fake) {
            $this->app->instance(MollieApiClient::class, $fake);
        });
    }
}
