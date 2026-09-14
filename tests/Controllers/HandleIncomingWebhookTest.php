<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\Controllers;

use Illuminate\Support\Facades\Event;
use Mollie\Api\Fake\MockEvent;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Laravel\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class HandleIncomingWebhookTest extends TestCase
{
    #[Test]
    #[DataProvider('signingSecrets')]
    public function it_validates_each_configured_signing_secret(string|array $secrets, string $signingSecret, bool $accepted): void
    {
        config(['mollie.webhooks.signing_secrets' => $secrets]);
        Event::fake();

        $payload = MockEvent::for(PaymentLinkPaid::class)->snapshot()->create();

        $response = $this->withHeader(
            SignatureValidator::SIGNATURE_HEADER,
            SignatureValidator::createSignature(json_encode($payload), $signingSecret)
        )->postJson(route('mollie.webhooks'), $payload);

        if ($accepted) {
            $response->assertSuccessful();
            Event::assertDispatched(PaymentLinkPaid::class);
        } else {
            $response->assertUnauthorized();
            Event::assertNotDispatched(PaymentLinkPaid::class);
        }
    }

    public static function signingSecrets(): array
    {
        return [
            'single secret' => ['new_secret', 'new_secret', true],
            'old rotation secret' => ['old_secret,new_secret', 'old_secret', true],
            'new rotation secret' => ['old_secret,new_secret', 'new_secret', true],
            'whitespace around secrets' => [' old_secret, new_secret ', 'new_secret', true],
            'empty list entries' => [',old_secret,,new_secret,', 'new_secret', true],
            'array old secret' => [['old_secret', 'new_secret'], 'old_secret', true],
            'array new secret' => [['old_secret', 'new_secret'], 'new_secret', true],
            'unknown secret' => ['old_secret,new_secret', 'unknown_secret', false],
            'literal comma-separated secret' => ['old_secret,new_secret', 'old_secret,new_secret', false],
            'empty secret' => [',old_secret,,new_secret,', '', false],
        ];
    }

    #[Test]
    public function it_can_handle_incoming_webhook()
    {
        $this->withoutExceptionHandling();
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
