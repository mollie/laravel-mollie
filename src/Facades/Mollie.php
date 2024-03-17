<?php

namespace Mollie\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Mollie\Api\MollieApiClient;
use Mollie\Laravel\MollieManager;

/**
 * (Facade) Class Mollie.
 *
 * @method static MollieApiClient api()
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
        return MollieManager::class;
    }
}
