<?php

declare(strict_types=1);

namespace Mollie\Laravel;

use Mollie\Api\Webhooks\Events\BaseEvent;
use Mollie\Laravel\Contracts\WebhookDispatcher;

class EventWebhookDispatcher implements WebhookDispatcher
{
    public function dispatch(BaseEvent $event): void
    {
        event($event);
    }
}
