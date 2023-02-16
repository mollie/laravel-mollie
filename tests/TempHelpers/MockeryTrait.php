<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\TempHelpers;

use Mockery;

trait MockeryTrait
{
    /**
     * Tear down mockery.
     *
     * @after
     *
     * @return void
     */
    public function tearDownMockery()
    {
        if (class_exists(Mockery::class, false)) {
            $container = Mockery::getContainer();

            if ($container) {
                $this->addToAssertionCount($container->mockery_getExpectationCount());
            }

            Mockery::close();
        }
    }
}
