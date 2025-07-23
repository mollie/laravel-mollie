<?php

declare(strict_types=1);

namespace Mollie\Laravel\Middlewares;

use Illuminate\Http\Request;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Mollie\Api\Exceptions\InvalidSignatureException;
use Mollie\Api\Webhooks\SignatureValidator;

class ValidatesWebhookSignatures
{
    public function handle(Request $request, Closure $next)
    {
        if (! config('mollie.webhooks.enabled')) {
            return $next($request);
        }

        try {
            $validator = new SignatureValidator(config('mollie.webhooks.signing_secrets'));

            /** @var string $body */
            $body = $request->getContent();
            $signature = $request->header('X-Mollie-Signature');

            $isLegacyWebhook = $validator->validatePayload(
                $body,
                $signature
            );

            if ($isLegacyWebhook && !config('mollie.webhooks.legacy_webhook_enabled')) {
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
