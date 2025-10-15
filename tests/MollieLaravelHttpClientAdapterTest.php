<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests;

use GuzzleHttp\Exception\ConnectException;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\Resources\Payment;
use Mollie\Laravel\Facades\Mollie;

/**
 * Class MollieLaravelHttpClientAdapterTest
 */
class MollieLaravelHttpClientAdapterTest extends TestCase
{
    public function test_post_request()
    {
        Mollie::fake([
            CreatePaymentRequest::class => MockResponse::resource(Payment::class)
                ->with([
                    'id' => $paymentId = uniqid('tr_'),
                    'redirectUrl' => $redirectUrl = 'https://google.com/redirect',
                    'description' => $description = 'test',
                ])
                ->create(),
        ]);

        /** @var Payment $returnedPayment */
        $returnedPayment = Mollie::api()->send(new CreatePaymentRequest(
            description: $description,
            amount: new Money('10.00', 'EUR'),
            redirectUrl: $redirectUrl,
        ));

        $this->assertEquals($paymentId, $returnedPayment->id);
        $this->assertEquals($redirectUrl, $returnedPayment->redirectUrl);
        $this->assertEquals($description, $returnedPayment->description);
    }

    public function test_get_request()
    {
        Mollie::fake([
            GetPaymentRequest::class => MockResponse::resource(Payment::class)
                ->with([
                    'id' => $paymentId = uniqid('tr_'),
                    'redirectUrl' => $redirectUrl = 'https://google.com/redirect',
                    'description' => $description = 'test',
                ])
                ->create(),
        ]);

        $returnedPayment = Mollie::api()->send(new GetPaymentRequest($paymentId));

        $this->assertEquals($paymentId, $returnedPayment->id);
        $this->assertEquals($redirectUrl, $returnedPayment->redirectUrl);
        $this->assertEquals($description, $returnedPayment->description);
    }

    public function test_exception_handling()
    {
        Mollie::fake([
            GetPaymentRequest::class => MockResponse::error(500, 'Internal Server Error', 'Internal Server Error'),
        ]);

        $this->expectException(ApiException::class);

        // This should throw an ApiException
        Mollie::api()->send(new GetPaymentRequest('non_existing_payment'));
    }

    public function test_connection_error_handling()
    {
        Mollie::fake([
            GetPaymentRequest::class => function (PendingRequest $pendingRequest) {
                throw new ConnectException('Connection error', $pendingRequest->createPsrRequest());
            },
        ]);

        $this->expectException(RetryableNetworkRequestException::class);
        $this->expectExceptionMessage('Connection error');

        Mollie::api()
            // set retry to 0 to exit early
            ->setRetryStrategy(new LinearRetryStrategy(0, 0))
            ->send(new GetPaymentRequest('any_payment_id'));
    }
}
