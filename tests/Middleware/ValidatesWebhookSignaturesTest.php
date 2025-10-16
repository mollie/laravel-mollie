<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\Middleware;

use Illuminate\Http\Request;
use Mollie\Laravel\Middleware\ValidatesWebhookSignatures;
use Mollie\Laravel\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ValidatesWebhookSignaturesTest extends TestCase
{
    #[Test]
    public function it_bypasses_validation_when_webhooks_are_disabled()
    {
        config(['mollie.webhooks.enabled' => false]);

        $request = Request::create('/webhook', 'POST');

        $middleware = resolve(ValidatesWebhookSignatures::class);

        $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertTrue(true);
    }
}
