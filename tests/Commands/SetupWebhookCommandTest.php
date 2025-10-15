<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\Commands;

use Illuminate\Support\Facades\Http;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Webhooks\WebhookEventType;
use Mollie\Laravel\Commands\SetupWebhookCommand;
use Mollie\Laravel\Facades\Mollie;
use Mollie\Laravel\Tests\TestCase;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Resources\Webhook;

class SetupWebhookCommandTest extends TestCase
{
    public function test_setup_webhook_command()
    {
        Mollie::fake([
            CreateWebhookRequest::class => MockResponse::resource(Webhook::class)
                ->with([
                    'id' => 'webhook_123',
                    'webhookSecret' => 'secret_123',
                ])
                ->create(),
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Test Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::ALL])
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsOutputToContain('Webhook created successfully')
            ->expectsOutputToContain('🤫 Add this secret to your .env file: secret_123')
            ->assertSuccessful();
    }
}
