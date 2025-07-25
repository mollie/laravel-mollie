<?php

namespace Mollie\Laravel;

use Illuminate\Http\Request;
use Mollie\Api\Exceptions\InvalidSignatureException;
use Mollie\Api\Webhooks\SignatureValidator as BaseSignatureValidator;

class SignatureValidator
{
    private BaseSignatureValidator $validator;

    public function __construct(BaseSignatureValidator $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Request $request): void
    {
        $body = (string) $request->getContent();
        $signatures = $request->header(BaseSignatureValidator::SIGNATURE_HEADER, '');

        try {
            $isLegacyWebhook = ! $this->validator->validatePayload(
                $body,
                $signatures
            );
        } catch (InvalidSignatureException $e) {
            $this->marshalInvalidSignatureException($e);
        }

        $this->abortIfLegacyWebhookIsDisabled($isLegacyWebhook);
    }

    /**
     * Handle the given invalid signature exception.
     */
    private function marshalInvalidSignatureException(InvalidSignatureException $e): void
    {
        abort(401, $e->getMessage());
    }

    private function abortIfLegacyWebhookIsDisabled(bool $isLegacyWebhook): void
    {
        abort_if($isLegacyWebhook && ! config('mollie.webhooks.legacy_webhook_enabled'), 403, 'Legacy webhook feature is disabled');
    }
}
