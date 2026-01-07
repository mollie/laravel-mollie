<?php

declare(strict_types=1);

namespace Mollie\Laravel\Contracts;

use Mollie\Api\Webhooks\Events\BaseEvent;

interface WebhookDispatcher
{
    public function dispatch(BaseEvent $event): void;
}
