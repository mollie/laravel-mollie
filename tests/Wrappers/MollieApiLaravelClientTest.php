<?php

namespace Mollie\Laravel\Tests\Wrappers;

use Illuminate\Support\Facades\Http;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Laravel\Tests\TestCase;
use Mollie\Laravel\Wrappers\MollieApiWrapper;

/**
 * Class MollieApiWrapper
 */
class MollieApiLaravelClientTest extends TestCase
{
    public function testPostRequest()
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

        $client->setApiKey('test_nawruSyCR7UE84EhtVmMmDGdRswBqj');
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

    public function testGetRequest()
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

        $client->setApiKey('test_nawruSyCR7UE84EhtVmMmDGdRswBqj');
        $returnedPayment = $client->payments->get($payment->id);

        $this->assertEquals($payment->id, $returnedPayment->id);
        $this->assertEquals($payment->redirectUrl, $returnedPayment->redirectUrl);
        $this->assertEquals($payment->description, $returnedPayment->description);
    }
}
