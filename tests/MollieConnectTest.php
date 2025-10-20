<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Laravel\Socialite\Facades\Socialite;
use Mollie\Laravel\MollieConnectProvider;
use PHPUnit\Framework\Attributes\Test;

class MollieConnectTest extends TestCase
{
    #[Test]
    public function it_can_resolve_mollie_socialite()
    {
        config(['services.mollie' => [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'redirect' => 'test_redirect',
        ]]);

        $this->assertInstanceOf(MollieConnectProvider::class, Socialite::with('mollie'));
    }
}
