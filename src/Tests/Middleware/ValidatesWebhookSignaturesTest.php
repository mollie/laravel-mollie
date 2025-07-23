<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Mockery;
use Mollie\Api\Exceptions\InvalidSignatureException;
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Laravel\Middleware\ValidatesWebhookSignatures;
use Mollie\Laravel\Tests\TestCase;

class ValidatesWebhookSignaturesTest extends TestCase
{
    private ValidatesWebhookSignatures $middleware;
    private Closure $next;

    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = new ValidatesWebhookSignatures(
            $this->app->make(SignatureValidator::class)
        );
        $this->next = function ($request) {
            return response('OK');
        };
    }

    public function test_bypasses_validation_when_webhooks_are_disabled()
    {
        config(['mollie.webhooks.enabled' => false]);
        $request = Request::create('/webhook', 'POST');

        // Act
        $response = $this->middleware->handle($request, $this->next);

        // Assert
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_validates_signature_when_webhooks_are_enabled()
    {
        // Arrange
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => 'secret1,secret2',
            'mollie.webhooks.legacy_webhook_enabled' => true,
        ]);

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', 'valid_signature');

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')
            ->once()
            ->with('secret1,secret2');
        $validatorMock->shouldReceive('validatePayload')
            ->once()
            ->with('{"id":"payment_123"}', 'valid_signature')
            ->andReturn(false); // Not a legacy webhook

        // Act
        $response = $this->middleware->handle($request, $this->next);

        // Assert
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_throws_http_response_exception_on_invalid_signature()
    {
        // Arrange
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => 'secret1,secret2',
        ]);

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', 'invalid_signature');

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')
            ->once()
            ->with('secret1,secret2');
        $validatorMock->shouldReceive('validatePayload')
            ->once()
            ->with('{"id":"payment_123"}', 'invalid_signature')
            ->andThrow(new InvalidSignatureException('Invalid signature'));

        // Act & Assert
        $this->expectException(HttpResponseException::class);

        try {
            $this->middleware->handle($request, $this->next);
        } catch (HttpResponseException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
            $this->assertEquals(
                '{"message":"Invalid webhook signature"}',
                $e->getResponse()->getContent()
            );
            throw $e;
        }
    }

    public function test_allows_legacy_webhook_when_legacy_is_enabled()
    {
        // Arrange
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => 'secret1,secret2',
            'mollie.webhooks.legacy_webhook_enabled' => true,
        ]);

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', 'legacy_signature');

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')
            ->once()
            ->with('secret1,secret2');
        $validatorMock->shouldReceive('validatePayload')
            ->once()
            ->with('{"id":"payment_123"}', 'legacy_signature')
            ->andReturn(true); // Is a legacy webhook

        // Act
        $response = $this->middleware->handle($request, $this->next);

        // Assert
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_throws_exception_for_legacy_webhook_when_legacy_is_disabled()
    {
        // Arrange
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => 'secret1,secret2',
            'mollie.webhooks.legacy_webhook_enabled' => false,
        ]);

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', 'legacy_signature');

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')
            ->once()
            ->with('secret1,secret2');
        $validatorMock->shouldReceive('validatePayload')
            ->once()
            ->with('{"id":"payment_123"}', 'legacy_signature')
            ->andReturn(true); // Is a legacy webhook

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Legacy webhook feature is disabled');

        $this->middleware->handle($request, $this->next);
    }

    public function test_handles_request_without_signature_header()
    {
        // Arrange
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => 'secret1,secret2',
        ]);

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', null);

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')
            ->once()
            ->with('secret1,secret2');
        $validatorMock->shouldReceive('validatePayload')
            ->once()
            ->with('{"id":"payment_123"}', null)
            ->andThrow(new InvalidSignatureException('No signature provided'));

        // Act & Assert
        $this->expectException(HttpResponseException::class);

        try {
            $this->middleware->handle($request, $this->next);
        } catch (HttpResponseException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
            $this->assertEquals(
                '{"message":"Invalid webhook signature"}',
                $e->getResponse()->getContent()
            );
            throw $e;
        }
    }

    public function test_handles_empty_request_body()
    {
        // Arrange
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => 'secret1,secret2',
        ]);

        $request = $this->createRequestWithSignature('', 'some_signature');

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')
            ->once()
            ->with('secret1,secret2');
        $validatorMock->shouldReceive('validatePayload')
            ->once()
            ->with('', 'some_signature')
            ->andReturn(false);

        // Act
        $response = $this->middleware->handle($request, $this->next);

        // Assert
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_uses_configured_signing_secrets()
    {
        // Arrange
        $customSecrets = 'custom_secret_1,custom_secret_2,custom_secret_3';
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => $customSecrets,
        ]);

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', 'valid_signature');

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')
            ->once()
            ->with($customSecrets);
        $validatorMock->shouldReceive('validatePayload')
            ->once()
            ->andReturn(false);

        // Act
        $response = $this->middleware->handle($request, $this->next);

        // Assert
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_passes_request_to_next_middleware_on_success()
    {
        // Arrange
        config([
            'mollie.webhooks.enabled' => true,
            'mollie.webhooks.signing_secrets' => 'secret1',
        ]);

        $request = $this->createRequestWithSignature('{"id":"payment_123"}', 'valid_signature');

        $validatorMock = Mockery::mock('overload:' . SignatureValidator::class);
        $validatorMock->shouldReceive('__construct')->once();
        $validatorMock->shouldReceive('validatePayload')->once()->andReturn(false);

        $nextCalled = false;
        $next = function ($request) use (&$nextCalled) {
            $nextCalled = true;

            return response('Next middleware called');
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertTrue($nextCalled);
        $this->assertEquals('Next middleware called', $response->getContent());
    }

    /**
     * Create a request with the specified body and signature header.
     */
    private function createRequestWithSignature(string $body, ?string $signature): Request
    {
        $request = Request::create('/webhook', 'POST', [], [], [], [], $body);

        if ($signature !== null) {
            $request->headers->set('X-Mollie-Signature', $signature);
        }

        return $request;
    }
}
