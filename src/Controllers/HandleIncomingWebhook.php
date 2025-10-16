<?php

declare(strict_types=1);

namespace Mollie\Laravel\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mollie\Api\Webhooks\WebhookEventMapper;
use Mollie\Laravel\Contracts\WebhookDispatcher;
use Mollie\Api\Webhooks\Events\BaseEvent;

class HandleIncomingWebhook extends Controller
{
    public function __invoke(
        Request $request,
        WebhookEventMapper $eventMapper,
        WebhookDispatcher $dispatcher
    ): JsonResponse {
        /** @var BaseEvent $event */
        $event = $eventMapper->processPayload($request->toArray());

        $dispatcher->dispatch($event);

        return response()->json();
    }
}
