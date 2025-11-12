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

    public function validate(Request $request): self
    {
        $body = (string) $request->getContent();
        $signatures = $request->header(BaseSignatureValidator::SIGNATURE_HEADER, '');

        try {
            $this->validator->validatePayload(
                $body,
                $signatures
            );
        } catch (InvalidSignatureException $e) {
            $this->marshalInvalidSignatureException($e);
        }

        return $this;
    }

    public function hasNoSignature(Request $request): bool
    {
        return ! $request->hasHeader(BaseSignatureValidator::SIGNATURE_HEADER);
    }

    /**
     * Handle the given invalid signature exception.
     */
    private function marshalInvalidSignatureException(InvalidSignatureException $e): void
    {
        abort(401, $e->getMessage());
    }
}
