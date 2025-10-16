<?php

namespace Mollie\Laravel\Tests\Controllers;

use Illuminate\Support\Facades\Event;
use Mollie\Api\Fake\MockEvent;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Laravel\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class HandleIncomingWebhookTest extends TestCase
{
    #[Test]
    public function it_can_handle_incoming_webhook()
    {
        config(['mollie.webhooks.signing_secrets' => 'test_secret']);

        Event::fake();

        $webhookPayload = MockEvent::for(PaymentLinkPaid::class)
            ->snapshot()
            ->create();

        $this
            ->withHeader(
                SignatureValidator::SIGNATURE_HEADER,
                SignatureValidator::createSignature(json_encode($webhookPayload), config('mollie.webhooks.signing_secrets'))
            )
            ->postJson(route('mollie.webhooks'), $webhookPayload)
            ->assertSuccessful();

        Event::assertDispatched(PaymentLinkPaid::class);
    }
}
