<?php

namespace Mollie\Laravel\Tests;

use GrahamCampbell\TestBenchCore\MockeryTrait;
use Illuminate\Support\Facades\Request;
use Mockery as m;
use Mollie\Laravel\MollieConnectProvider;

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
        $session->shouldReceive('set')->once();
        $provider = new MollieConnectProvider($request, 'client_id', 'client_secret', 'redirect');
        $response = $provider->redirect();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertContains(
            'https://www.mollie.com/oauth2/authorize?client_id=client_id&redirect_uri=redirect&scope=organizations.read&response_type=code&state=',
            $response->getTargetUrl()
        );
    }

    /**
     * @expectedException \Laravel\Socialite\Two\InvalidStateException
     */
    public function testExceptionIsThrownIfStateIsInvalid()
    {
        $request = Request::create('foo', 'GET', ['state' => str_repeat('B', 40), 'code' => 'code']);
        $request->setSession($session = m::mock('Symfony\Component\HttpFoundation\Session\SessionInterface'));
        $session->shouldReceive('pull')->once()->with('state')->andReturn(str_repeat('A', 40));
        $provider = new MollieConnectProvider($request, 'client_id', 'client_secret', 'redirect');
        $user = $provider->user();
    }

    /**
     * @expectedException \Laravel\Socialite\Two\InvalidStateException
     */
    public function testExceptionIsThrownIfStateIsNotSet()
    {
        $request = Request::create('foo', 'GET', ['state' => 'state', 'code' => 'code']);
        $request->setSession($session = m::mock('Symfony\Component\HttpFoundation\Session\SessionInterface'));
        $session->shouldReceive('pull')->once()->with('state');
        $provider = new MollieConnectProvider($request, 'client_id', 'client_secret', 'redirect');
        $user = $provider->user();
    }

}