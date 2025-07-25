<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use Illuminate\Http\Request;
use Mollie\Api\Webhooks\SignatureValidator as BaseSignatureValidator;
use Mollie\Laravel\SignatureValidator;
use Mollie\Laravel\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SignatureValidatorTest extends TestCase
{
    public function test_validates_signature_with_valid_secret()
    {
        config([
            'mollie.webhooks.legacy_webhook_enabled' => false
        ]);

        $body = '{"id":"payment_123"}';
        $request = $this->createRequestWithSignature($body, 'valid_secret');

        $validator = new SignatureValidator(new BaseSignatureValidator('valid_secret'));

        $validator->validate($request);

        $this->assertTrue(true);
    }

    public function test_allows_legacy_webhook_when_legacy_is_enabled()
    {
        config(['mollie.webhooks.legacy_webhook_enabled' => true]);

        $body = '{"id":"payment_123"}';
        $request = $this->createRequest($body);

        $validator = new SignatureValidator(new BaseSignatureValidator('some_secret'));

        $validator->validate($request);

        $this->assertTrue(true);
    }

    public function test_throws_http_response_exception_on_invalid_signature()
    {
        config(['mollie.webhooks.legacy_webhook_enabled' => false]);

        $validator = new SignatureValidator(new BaseSignatureValidator('valid_secret'));

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', 'invalid_secret');

        try {
            $validator->validate($request);
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Invalid webhook signature', $e->getMessage());
        }
    }

    public function test_aborts_if_legacy_webhook_is_disabled()
    {
        config(['mollie.webhooks.legacy_webhook_enabled' => false]);

        $body = '{"id":"payment_123"}';
        $request = $this->createRequest($body);

        $validator = new SignatureValidator(new BaseSignatureValidator('some_secret'));

        try {
            $validator->validate($request);
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Legacy webhook feature is disabled', $e->getMessage());
        }
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
