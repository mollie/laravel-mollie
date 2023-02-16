<?php

namespace Mollie\Laravel\Tests;

use Illuminate\Support\Facades\Request;
use Mockery as m;
use Mollie\Laravel\MollieConnectProvider;
use Mollie\Laravel\Tests\TempHelpers\MockeryTrait;

class MollieConnectProviderTest extends TestCase
{
    use MockeryTrait;

    /**
     * @before
     */
    public function verifySocialite()
    {
        if (! interface_exists('Laravel\Socialite\Contracts\Factory')) {
            $this->markTestSkipped('Laravel Socialite must be present.');
        }
    }

    public function testRedirectGeneratesTheProperSymfonyRedirectResponse()
    {
        $request = Request::create('foo');
        $request->setSession($session = m::mock('Symfony\Component\HttpFoundation\Session\SessionInterface'));
        $session->shouldReceive('put')->once();
        $provider = new MollieConnectProvider($request, 'client_id', 'client_secret', 'redirect');
        $response = $provider->redirect();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertStringStartsWith(
            'https://www.mollie.com/oauth2/authorize?client_id=client_id&redirect_uri=redirect&scope=organizations.read&response_type=code&state=',
            $response->getTargetUrl()
        );
    }

    public function testExceptionIsThrownIfStateIsInvalid()
    {
        $this->expectException(\Laravel\Socialite\Two\InvalidStateException::class);
        $request = Request::create('foo', 'GET', ['state' => str_repeat('B', 40), 'code' => 'code']);
        $request->setSession($session = m::mock('Symfony\Component\HttpFoundation\Session\SessionInterface'));
        $session->shouldReceive('pull')->once()->with('state')->andReturn(str_repeat('A', 40));
        $provider = new MollieConnectProvider($request, 'client_id', 'client_secret', 'redirect');
        $user = $provider->user();
    }

    public function testExceptionIsThrownIfStateIsNotSet()
    {
        $this->expectException(\Laravel\Socialite\Two\InvalidStateException::class);
        $request = Request::create('foo', 'GET', ['state' => 'state', 'code' => 'code']);
        $request->setSession($session = m::mock('Symfony\Component\HttpFoundation\Session\SessionInterface'));
        $session->shouldReceive('pull')->once()->with('state');
        $provider = new MollieConnectProvider($request, 'client_id', 'client_secret', 'redirect');
        $user = $provider->user();
    }

    public function testGetTokenFields()
    {
        $request = Request::create('foo');
        $provider = new MollieConnectProvider($request, 'client_id', 'client_secret', 'redirect');
        $this->assertEquals(
            [
                'client_id' => 'client_id',
                'client_secret' => 'client_secret',
                'code' => 'dummy_code',
                'redirect_uri' => 'redirect',
                'grant_type' => 'authorization_code',
            ],
            $provider->getTokenFields('dummy_code')
        );
    }
}
