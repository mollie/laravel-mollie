<?php

namespace Mollie\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Auth\TokenValidator;
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\Http\Requests\ListPermissionsRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Webhooks\WebhookEventType;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\pause;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class SetupWebhookCommand extends Command
{
    protected $signature = 'mollie:setup-webhook';

    protected $description = 'Setup and create a webhook in your Mollie account';

    public function handle(MollieApiClient $mollie): int
    {
        $webhookDetails = $this->askForWebhookDetails();

        clear();

        if (! $this->confirmDetails($webhookDetails)) {
            info('Webhook setup cancelled');

            return Command::SUCCESS;
        }

        if (! $this->hasValidAccessToken()) {
            info('To automatically create the webhook, you need to provide an access token.');
            $this->suggestManualCreation();

            return Command::SUCCESS;
        }

        if (! $this->hasWebhookWritePermission($mollie)) {
            warning('You do not have permission to create webhooks. You will need to create the webhook manually in the Mollie dashboard.');
            warning('Or create an access token with the permission of webhooks.write');

            $this->suggestManualCreation();

            return Command::SUCCESS;
        }

        pause('Press ENTER to continue');

        $webhook = $this->createWebhook($mollie, $webhookDetails);

        if (! $webhook) {
            return Command::FAILURE;
        }

        $this->displaySuccess($webhook);

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
                ['name' => $responses['name'], 'url' => $responses['url'], 'events' => Arr::join($responses['events'], ', '), 'testmode' => $responses['testmode']]
            ]
        );

        return confirm(
            label: 'Proceed with setup?',
            default: true,
            required: true
        );
    }

    private function hasValidAccessToken(): bool
    {
        return TokenValidator::isAccessToken(config('mollie.key'));
    }

    private function hasWebhookWritePermission(MollieApiClient $mollie): bool
    {
        return $mollie->send(new ListPermissionsRequest())
            ->contains(function (Permission $permission) {
                return $permission->id === 'webhooks.write'
                    && $permission->granted;
            });
    }

    private function createWebhook(MollieApiClient $mollie, array $webhookDetails): ?Webhook
    {
        try {
            $request = new CreateWebhookRequest(
                url: $webhookDetails['url'],
                name: $webhookDetails['name'],
                eventTypes: $webhookDetails['events'],
            );

            return spin(
                fn () => $mollie->send($request->test($webhookDetails['testmode'] === 'yes')),
                message: 'Creating webhook...'
            );
        } catch (MollieException $e) {
            error('Failed to create webhook: '.$e->getMessage());

            return null;
        }
    }

    private function displaySuccess(Webhook $webhook): void
    {
        info('Webhook created successfully');
        note('🤫 Add this secret to your .env file: '.$webhook->webhookSecret);

        $existingSecrets = config('mollie.webhooks.signing_secrets');

        note(
            'MOLLIE_WEBHOOK_SIGNING_SECRETS='.
            str($existingSecrets)
                ->whenNotEmpty(fn ($str) => $str->append(','))
                ->append($webhook->webhookSecret)
        );
    }

    private function suggestManualCreation(): void
    {
        info('Otherwise, you can create the webhook manually in the Mollie dashboard.');
        info('The webhook path is: '.config('mollie.webhooks.path'));

        note('Make sure to append the webhook secret to the .env file: MOLLIE_WEBHOOK_SIGNING_SECRETS=...');
    }
}
