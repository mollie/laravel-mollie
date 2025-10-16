<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\Commands;

use Illuminate\Support\Arr;
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\Webhooks\WebhookEventType;
use Mollie\Laravel\Commands\SetupWebhookCommand;
use Mollie\Laravel\Facades\Mollie;
use Mollie\Laravel\Tests\TestCase;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\ListPermissionsRequest;
use Mollie\Api\Resources\PermissionCollection;
use Mollie\Api\Resources\Webhook;
use PHPUnit\Framework\Attributes\Test;

class SetupWebhookCommandTest extends TestCase
{
    #[Test]
    public function it_can_setup_a_webhook_automatically()
    {
        config(['mollie.key' => 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);

        Mollie::fake([
            ListPermissionsRequest::class => MockResponse::list(PermissionCollection::class)
                ->add([
                    'id' => 'webhooks.write',
                    'description' => 'Write webhooks',
                    'granted' => true,
                ])
                ->create(),
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
            ->expectsQuestion('Press ENTER to continue', 'yes')
            ->expectsOutputToContain('Webhook created successfully')
            ->expectsOutputToContain('🤫 Add this secret to your .env file: secret_123')
            ->assertSuccessful();

        Mollie::assertSent(CreateWebhookRequest::class);
        Mollie::assertSent(ListPermissionsRequest::class);
    }

    #[Test]
    public function it_can_setup_webhook_with_payment_link_event()
    {
        config(['mollie.key' => 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);

        Mollie::fake([
            ListPermissionsRequest::class => MockResponse::list(PermissionCollection::class)
                ->add([
                    'id' => 'webhooks.write',
                    'description' => 'Write webhooks',
                    'granted' => true,
                ])
                ->create(),
            CreateWebhookRequest::class => function (PendingRequest $pendingRequest) {
                $this->assertEquals(WebhookEventType::PAYMENT_LINK_PAID, $pendingRequest->payload()->get('eventTypes'));

                return MockResponse::resource(Webhook::class)
                    ->with([
                        'id' => 'webhook_payment_link',
                        'webhookSecret' => 'secret_payment_link',
                    ])
                    ->create();
            },
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Payment Link Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::PAYMENT_LINK_PAID])
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsQuestion('Press ENTER to continue', 'yes')
            ->expectsOutputToContain('Webhook created successfully')
            ->expectsOutputToContain('secret_payment_link')
            ->assertSuccessful();

        Mollie::assertSent(CreateWebhookRequest::class);
    }

    #[Test]
    public function it_suggests_manual_creation_when_no_access_token_is_provided()
    {
        config([
            'mollie.key' => 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz',
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Test Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::ALL])
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsOutputToContain('To automatically create the webhook, you need to provide an access token')
            ->expectsOutputToContain('you can create the webhook manually in the Mollie dashboard')
            ->assertSuccessful();
    }

    #[Test]
    public function it_suggests_manual_creation_when_missing_webhook_write_permission()
    {
        config(['mollie.key' => 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);

        Mollie::fake([
            ListPermissionsRequest::class => MockResponse::list(PermissionCollection::class)
                ->add([
                    'id' => 'payments.read',
                    'description' => 'Read payments',
                    'granted' => true,
                ])
                ->create(),
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Test Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::ALL])
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsOutputToContain('You do not have permission to create webhooks')
            ->expectsOutputToContain('create an access token with the permission of webhooks.write')
            ->expectsOutputToContain('you can create the webhook manually in the Mollie dashboard')
            ->assertSuccessful();

        Mollie::assertSent(ListPermissionsRequest::class);
    }

    #[Test]
    public function it_can_setup_webhook_in_live_mode()
    {
        config(['mollie.key' => 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);

        Mollie::fake([
            ListPermissionsRequest::class => MockResponse::list(PermissionCollection::class)
                ->add([
                    'id' => 'webhooks.write',
                    'description' => 'Write webhooks',
                    'granted' => true,
                ])
                ->create(),
            CreateWebhookRequest::class => function (PendingRequest $pendingRequest) {
                $this->assertEquals($pendingRequest->getTestmode(), false);

                return MockResponse::resource(Webhook::class)
                    ->with([
                        'id' => 'webhook_live_123',
                        'webhookSecret' => 'secret_live_123',
                    ])
                    ->create();
            },
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Production Webhook')
            ->expectsQuestion('Url', 'https://production.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::ALL])
            ->expectsQuestion('Testmode', 'no')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsQuestion('Press ENTER to continue', 'yes')
            ->expectsOutputToContain('Webhook created successfully')
            ->expectsOutputToContain('🤫 Add this secret to your .env file: secret_live_123')
            ->assertSuccessful();

        Mollie::assertSent(CreateWebhookRequest::class);
    }

    #[Test]
    public function it_can_setup_webhook_with_specific_event_types()
    {
        config(['mollie.key' => 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz']);

        $eventTypes = [
            WebhookEventType::SALES_INVOICE_CREATED,
            WebhookEventType::SALES_INVOICE_PAID,
            WebhookEventType::BALANCE_TRANSACTION_CREATED,
        ];

        Mollie::fake([
            ListPermissionsRequest::class => MockResponse::list(PermissionCollection::class)
                ->add([
                    'id' => 'webhooks.write',
                    'description' => 'Write webhooks',
                    'granted' => true,
                ])
                ->create(),
            CreateWebhookRequest::class => function (PendingRequest $pendingRequest) use ($eventTypes) {
                $this->assertEquals(Arr::join($eventTypes, ','), $pendingRequest->payload()->get('eventTypes'));

                return MockResponse::resource(Webhook::class)
                    ->with([
                        'id' => 'webhook_456',
                        'webhookSecret' => 'secret_456',
                    ])
                    ->create();
            },
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Invoice Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', $eventTypes)
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsQuestion('Press ENTER to continue', 'yes')
            ->expectsOutputToContain('Webhook created successfully')
            ->assertSuccessful();

        Mollie::assertSent(CreateWebhookRequest::class);
    }

    #[Test]
    public function it_appends_secret_to_existing_secrets_in_output()
    {
        config([
            'mollie.key' => 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz',
            'mollie.webhooks.signing_secrets' => 'existing_secret_1,existing_secret_2',
        ]);

        Mollie::fake([
            ListPermissionsRequest::class => MockResponse::list(PermissionCollection::class)
                ->add([
                    'id' => 'webhooks.write',
                    'description' => 'Write webhooks',
                    'granted' => true,
                ])
                ->create(),
            CreateWebhookRequest::class => MockResponse::resource(Webhook::class)
                ->with([
                    'id' => 'webhook_789',
                    'webhookSecret' => 'secret_new_789',
                ])
                ->create(),
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Additional Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::ALL])
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsQuestion('Press ENTER to continue', 'yes')
            ->expectsOutputToContain('MOLLIE_WEBHOOK_SIGNING_SECRETS=existing_secret_1,existing_secret_2,secret_new_789')
            ->assertSuccessful();
    }

    #[Test]
    public function it_displays_correct_format_for_first_secret()
    {
        config([
            'mollie.key' => 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz',
            'mollie.webhooks.signing_secrets' => '',
        ]);

        Mollie::fake([
            ListPermissionsRequest::class => MockResponse::list(PermissionCollection::class)
                ->add([
                    'id' => 'webhooks.write',
                    'description' => 'Write webhooks',
                    'granted' => true,
                ])
                ->create(),
            CreateWebhookRequest::class => MockResponse::resource(Webhook::class)
                ->with([
                    'id' => 'webhook_first',
                    'webhookSecret' => 'secret_first',
                ])
                ->create(),
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'First Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::ALL])
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsQuestion('Press ENTER to continue', 'yes')
            ->expectsOutputToContain('MOLLIE_WEBHOOK_SIGNING_SECRETS=secret_first')
            ->assertSuccessful();
    }

    #[Test]
    public function it_displays_webhook_path_in_manual_creation_message()
    {
        config([
            'mollie.key' => 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxyz',
        ]);

        $this->artisan(SetupWebhookCommand::class)
            ->expectsQuestion('Name', 'Test Webhook')
            ->expectsQuestion('Url', 'https://test.com/webhook')
            ->expectsQuestion('Events', [WebhookEventType::ALL])
            ->expectsQuestion('Testmode', 'yes')
            ->expectsConfirmation('Proceed with setup?', 'yes')
            ->expectsOutputToContain('To automatically create the webhook, you need to provide an access token')
            ->expectsOutputToContain('you can create the webhook manually in the Mollie dashboard')
            ->expectsOutputToContain('The webhook path is: ' . config('mollie.webhooks.path'))
            ->assertSuccessful();
    }
}
