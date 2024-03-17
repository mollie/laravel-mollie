<?php

namespace Mollie\Laravel;

use Illuminate\Contracts\Container\Container;
use Mollie\Api\MollieApiClient;

class MollieManager
{
    public function __construct(private Container $app)
    {
    }

    public function api(): MollieApiClient
    {
        return $this->app->make(MollieApiClient::class);
    }
}
