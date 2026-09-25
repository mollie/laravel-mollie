<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Illuminate\Http\Request;
use Mollie\Api\Webhooks\SignatureValidator as BaseSignatureValidator;
use Mollie\Laravel\SignatureValidator;
use PHPUnit\Framework\Attributes\Test;

class SignatureValidatorTest extends TestCase
{
    #[Test]
    public function it_validates_signature_with_valid_secret()
    {
        $body = '{"id":"payment_123"}';
        $request = $this->createRequestWithSignature($body, 'valid_secret');

        $validator = new SignatureValidator(new BaseSignatureValidator('valid_secret'));

        $validator->validate($request);

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function it_rejects_a_request_without_a_signature()
    {
        $request = $this->createRequest('{"id":"payment_123"}');
        $validator = new SignatureValidator(new BaseSignatureValidator('valid_secret'));

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $validator->validate($request);
    }

    /**
     * Create a request with the specified body and signature header.
     */
    private function createRequestWithSignature(string $body, string $secret): Request
    {
        $request = $this->createRequest($body);

        $request->headers->set(BaseSignatureValidator::SIGNATURE_HEADER, BaseSignatureValidator::createSignature($body, $secret));

        return $request;
    }

    private function createRequest(string $body): Request
    {
        return Request::create('/webhook', 'POST', [], [], [], [], $body);
    }
}
