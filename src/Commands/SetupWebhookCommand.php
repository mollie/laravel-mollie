<?php

namespace Mollie\Laravel\Commands;

use Illuminate\Console\Command;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Webhooks\WebhookEventType;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\form;
use function Laravel\Prompts\table;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

class SetupWebhookCommand extends Command
{
    protected $signature = 'mollie:setup-webhook';

    protected $description = 'Setup the webhook';

    public function handle(MollieApiClient $mollie)
    {
        $responses = $this->askForWebhookDetails();

        clear();

        $proceed = $this->confirmDetails($responses);

        if (! $proceed) {
            $this->info('Webhook setup cancelled');

            return Command::SUCCESS;
        }

        try {
            $request = new CreateWebhookRequest(
                url: $responses['url'],
                name: $responses['name'],
                eventTypes: $responses['events'],
            );

            /** @var Webhook $response */
            $response = spin(
                fn () => $mollie->send($request->test($responses['testmode'] === 'yes')),
                message: 'Creating webhook...'
            );

        } catch (MollieException $e) {
            error('Failed to create webhook: ' . $e->getMessage());

            return Command::FAILURE;
        }

        info('Webhook created successfully');
        note('🤫 Add this secret to your .env file: ' . $response->webhookSecret);

        $existingSecrets = config('mollie.webhooks.signing_secrets');

        note(
            'MOLLIE_WEBHOOK_SIGNING_SECRETS=' .
            str($existingSecrets)
                ->whenNotEmpty(
                    fn ($str) => $str->append(','),
                )->append($response->webhookSecret)
        );

        return Command::SUCCESS;

    }

    private function askForWebhookDetails(): array
    {
        return form()
            ->text(
                label: 'Name',
                placeholder: 'Webhook name',
                default: config('app.name'),
                required: true,
                name: 'name'
            )
            ->text(
                label: 'Url',
                placeholder: 'Incoming webhook url',
                default: route('mollie.webhooks'),
                required: true,
                validate: ['url' => 'required|url'],
                name: 'url'
            )
            ->multiselect(
                label: 'Events',
                options: WebhookEventType::getAllNextGenWebhookEventTypes(),
                default: [WebhookEventType::ALL],
                required: true,
                name: 'events'
            )
            ->select(
                label: 'Testmode',
                options: ['yes', 'no'],
                default: 'yes',
                required: true,
                name: 'testmode'
            )
            ->submit();
    }

    private function confirmDetails(array $responses): bool
    {
        table(
            headers: ['Name', 'Url', 'Events', 'Testmode'],
            rows: [
                [$responses['name'], $responses['url'], $responses['events'], $responses['testmode']]
            ]
        );

        return confirm(
            label: 'Proceed with setup?',
            default: true,
            required: true
        );
    }
}
