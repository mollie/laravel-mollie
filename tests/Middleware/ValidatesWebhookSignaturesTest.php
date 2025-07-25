<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\Middleware;

use Illuminate\Http\Request;
use Mollie\Laravel\Middleware\ValidatesWebhookSignatures;
use Mollie\Laravel\Tests\TestCase;

class ValidatesWebhookSignaturesTest extends TestCase
{
    public function test_bypasses_validation_when_webhooks_are_disabled()
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
