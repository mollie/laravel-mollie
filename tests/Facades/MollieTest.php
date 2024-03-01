<?php

namespace Mollie\Laravel\Tests\Facades;

use Mollie\Laravel\Facades\Mollie;
use Mollie\Laravel\MollieManager;
use Mollie\Laravel\Tests\TempHelpers\FacadeTrait;
use Mollie\Laravel\Tests\TestCase;

/**
 * This is the MollieTest facade test class.
 */
class MollieTest extends TestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return MollieManager::class;
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return Mollie::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return MollieManager::class;
    }
}
