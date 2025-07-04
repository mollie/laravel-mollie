<?php

namespace Mollie\Laravel\Tests;

use Illuminate\Support\Facades\Http;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

/**
 * Class MollieLaravelHttpClientAdapterTest
 */
class MollieLaravelHttpClientAdapterTest extends TestCase
{
    public function test_post_request()
    {
        /** @var MollieApiClient $client */
        $client = app(MollieApiClient::class);
        $payment = new Payment($client);
        $payment->id = uniqid('tr_');
        $payment->redirectUrl = 'https://google.com/redirect';
        $payment->description = 'test';

        Http::fake([
            'https://api.mollie.com/*' => Http::response(json_encode($payment)),
        ]);

        $returnedPayment = $client->payments->create([
            'redirectUrl' => 'https://google.com/redirect',
            'description' => 'test',
            'amount' => [
                'value' => '10.00',
                'currency' => 'EUR',
            ],
        ]);

        $this->assertEquals($payment->id, $returnedPayment->id);
        $this->assertEquals($payment->redirectUrl, $returnedPayment->redirectUrl);
        $this->assertEquals($payment->description, $returnedPayment->description);
    }

    public function test_get_request()
    {
        /** @var MollieApiClient $client */
        $client = app(MollieApiClient::class);
        $payment = new Payment($client);
        $payment->id = uniqid('tr_');
        $payment->redirectUrl = 'https://google.com/redirect';
        $payment->description = 'test';

        Http::fake([
            'https://api.mollie.com/v2/payments/'.$payment->id => Http::response(json_encode($payment)),
        ]);

        $returnedPayment = $client->payments->get($payment->id);

        $this->assertEquals($payment->id, $returnedPayment->id);
        $this->assertEquals($payment->redirectUrl, $returnedPayment->redirectUrl);
        $this->assertEquals($payment->description, $returnedPayment->description);
    }
    
    public function test_exception_handling()
    {
        /** @var MollieApiClient $client */
        $client = app(MollieApiClient::class);
        
        // Simulate a network error
        Http::fake([
            'https://api.mollie.com/*' => Http::response('', 500),
        ]);
        
        $this->expectException(ApiException::class);
        
        // This should throw an ApiException
        $client->payments->get('non_existing_payment');
    }
    
    public function test_connection_error_handling()
    {
        /** @var MollieApiClient $client */
        $client = app(MollieApiClient::class);
        
        // Simulate a connection error
        Http::fake([
            'https://api.mollie.com/*' => function() {
                throw new \Exception('Connection error');
            },
        ]);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Connection error');
        
        // This should throw an ApiException with the connection error message
        $client->payments->get('any_payment_id');
    }
}
