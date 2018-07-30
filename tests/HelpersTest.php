<?php

namespace Mollie\Laravel\Tests;

class HelpersTest extends TestCase
{
    public function testItCanWorkWithHelper()
    {
        $this->assertTrue(function_exists('mollie'));

        $this->assertInstanceOf('\Mollie\Laravel\Wrappers\MollieApiWrapper', mollie());
    }
}
