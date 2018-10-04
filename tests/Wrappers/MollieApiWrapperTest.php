<?php

namespace Mollie\Laravel\Tests\Wrappers;

use Mollie\Api\MollieApiClient;
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
        $this->api = $this->getMockBuilder(MollieApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstruct()
    {
        $wrapper = new MollieApiWrapper($this->app['config'], $this->app[MollieApiClient::class]);
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
        $this->api->expects($this->atLeastOnce())->method('setApiKey')->with('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

        $wrapper = new MollieApiWrapper($this->app['config'], $this->api);
        $wrapper->setApiKey('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }

    /**
     * @expectedException Mollie\Api\Exceptions\ApiException
     * @expectedExceptionMessage Invalid API key: 'live_'. An API key must start with 'test_' or 'live_' and must be at least 30 characters long.
     */
    public function testSetBadApiKey()
    {
        $wrapper = new MollieApiWrapper($this->app['config'], $this->app[MollieApiClient::class]);
        $wrapper->setApiKey('live_');
    }

    public function testSetGoodToken()
    {
        $this->api->expects($this->once())->method('setAccessToken')->with('access_xxx');

        $wrapper = new MollieApiWrapper($this->app['config'], $this->api);
        $wrapper->setAccessToken('access_xxx');
    }

    /**
     * @expectedException Mollie\Api\Exceptions\ApiException
     * @expectedExceptionMessage Invalid OAuth access token: 'BAD'. An access token must start with 'access_'.
     */
    public function testSetBadToken()
    {
        $wrapper = new MollieApiWrapper($this->app['config'], $this->app[MollieApiClient::class]);
        $wrapper->setAccessToken('BAD');
    }

    public function testWrappedEndpoints()
    {
        $endpoints = [
            'customers',
            'customerPayments',
            'invoices',
            'mandates',
            'methods',
            'mandates',
            'orders',
            'organizations',
            'permissions',
            'payments',
            'profiles',
            'refunds',
            'settlements',
            'subscriptions',
        ];

        $client = $this->app[MollieApiClient::class];
        $wrapper = new MollieApiWrapper(
            $this->app['config'],
            $client
        );

        foreach ($endpoints as $endpoint) {
            $this->assertWrappedEndpoint($client, $wrapper, $endpoint);
        }
    }

    /**
     * Asserts that the referenced wrapper method matches the client attribute
     * I.e. $wrapper->payments() returns the same as $client->payments.
     *
     * @param  MollieApiClient $client
     * @param  MollieApiWrapper $wrapper
     * @param  string $reference
     * @return null
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function assertWrappedEndpoint($client, $wrapper, $reference)
    {
        $this->assertEquals($client->$reference, $wrapper->$reference());
    }
}
