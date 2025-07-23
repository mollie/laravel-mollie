<?php

declare(strict_types=1);

namespace Mollie\Laravel\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Mollie\Api\Exceptions\InvalidSignatureException;
use Mollie\Api\Webhooks\SignatureValidator;

class ValidatesWebhookSignatures
{
    public function __construct(
        private SignatureValidator $validator
    ) {}

    public function handle(Request $request, Closure $next)
    {
        if (! config('mollie.webhooks.enabled')) {
            return $next($request);
        }

        try {
            /** @var string $body */
            $body = $request->getContent();
            $signature = $request->header('X-Mollie-Signature');

            $isLegacyWebhook = $this->validator->validatePayload(
                $body,
                $signature
            );

            if ($isLegacyWebhook && ! config('mollie.webhooks.legacy_webhook_enabled')) {
                throw new \Exception('Legacy webhook feature is disabled');
            }

            return $next($request);

        } catch (InvalidSignatureException $e) {
            $response = response()->json([
                'message' => 'Invalid webhook signature',
            ], 400);

            throw new HttpResponseException($response, $e);
        }
    }
}
