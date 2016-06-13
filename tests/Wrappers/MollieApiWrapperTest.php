<?php

namespace Mollie\Laravel\Tests\Wrappers;

use Mollie_API_Client;
use Mollie_API_Exception;
use Mollie\Laravel\Tests\TestCase;
use Mollie\Laravel\Wrappers\MollieApiWrapper;

/**
 * Class MollieApiWrapper
 *
 * @package Mollie\Laravel\Tests\Wrappers
 */
class MollieApiWrapperTest extends TestCase
{
    /**
     * API Client mock.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $api;

    /**
     * @before
     */
    protected function setUpApi()
    {
        $this->api = $this->getMockBuilder(Mollie_API_Client::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstruct()
    {
        $wrapper = new MollieApiWrapper($this->app['config'], $this->app[Mollie_API_Client::class]);
        $this->assertInstanceOf(MollieApiWrapper::class, $wrapper);
    }

    public function testApiEndpoint()
    {
        $this->api->expects($this->once())->method('setApiEndpoint');
        $this->api->expects($this->once())->method('getApiEndpoint')->willReturn('/test');

        $wrapper = new MollieApiWrapper($this->app['config'], $this->api);

        $wrapper->setApiEndpoint('/test');
        $this->assertSame('/test', $wrapper->getApiEndpoint());
    }

    public function testSetGoodApiKey()
    {
        $this->api->expects($this->once())->method('setApiKey')->with('live_xxx');

        $wrapper = new MollieApiWrapper($this->app['config'], $this->api);
        $wrapper->setApiKey('live_xxx');
    }

    /**
     * @expectedException Mollie_API_Exception
     * @expectedExceptionMessage Invalid API key: 'live_'. An API key must start with 'test_' or 'live_'.
     */
    public function testSetBadApiKey()
    {
        $wrapper = new MollieApiWrapper($this->app['config'], $this->app[Mollie_API_Client::class]);
        $wrapper->setApiKey('live_');
    }

    public function testSetGoodToken()
    {
        $this->api->expects($this->once())->method('setAccessToken')->with('access_xxx');

        $wrapper = new MollieApiWrapper($this->app['config'], $this->api);
        $wrapper->setAccessToken('access_xxx');
    }

    /**
     * @expectedException Mollie_API_Exception
     * @expectedExceptionMessage Invalid OAuth access token: 'BAD'. An access token must start with 'access_'.
     */
    public function testSetBadToken()
    {
        $wrapper = new MollieApiWrapper($this->app['config'], $this->app[Mollie_API_Client::class]);
        $wrapper->setAccessToken('BAD');
    }
}