![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Process real-time status updates with a webhook
A webhook is a URL that Mollie calls when an object's status changes, for example when a payment transitions from `open` to `paid`. For more details, see [the webhook guide](https://docs.mollie.com/reference/webhooks-new).

## Next-gen Webhooks
Mollie's legacy webhooks only send the ID of updated resources, which requires your app to fetch the resource and determine what changed (see https://docs.mollie.com/reference/webhooks). Besides requiring an extra HTTP call, you'd have to handle IDs from potentially malicious sources without being able to verify their authenticity, risking information exposure to attackers.

Next-gen webhooks solve this through a signature sent in the header of the webhook request, along with the data of the resource that changed if you create a snapshot webhook.

### Setup
If you want to benefit from the Mollie webhook features provided by this package, set `MOLLIE_WEBHOOKS_ENABLED=true` in your `config/mollie.php` file.

Next, decide how webhook events should be handled in your app. By default, an event is dispatched (e.g. `PaymentLinkPaid`) that you can listen to throughout your application. To learn how to consume these webhook events, see [Generating Events and Listeners](https://laravel.com/docs/12.x/events#generating-events-and-listeners).

> [!NOTE]
> If you want to handle webhook requests differently, you can create your own dispatcher by implementing `Mollie\Laravel\Contracts\WebhookDispatcher` and setting `mollie.webhooks.dispatcher` to your custom class.

Finally, run the `mollie:setup-webhook` command to create a webhook.

## Legacy Webhooks
To implement legacy webhooks in your Laravel application, provide a `webhookUrl` parameter when creating a payment (or subscription):

```php
$payment = Mollie::api()->payments->create([
    'amount' => [
        'currency' => 'EUR',
        'value' => '10.00', // You must send the correct number of decimals, thus we enforce the use of strings
    ],
    'description' => 'My first API payment',
    'redirectUrl' => 'https://webshop.example.org/order/12345/',
    'webhookUrl'   => route('webhooks.mollie'),
]);
```

Create a route and apply the `ValidatesWebhookSignatures` middleware to accept incoming webhook requests:

```php
use Mollie\Laravel\Middleware\ValidatesWebhookSignatures;

Route::name('webhooks.mollie')
    ->middleware(ValidatesWebhookSignatures::class)
    ->post('webhooks/mollie', HandleIncomingWebhooks::class);
```

Then create a matching controller:

```php
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HandleIncomingWebhooks extends Controller
{
    public function __invoke(MollieApiClient $client, Request $request)
    {
        // Fetch the resource using the ID from the request
        $payment = $client->send(new GetPaymentRequest($request->input('id')));

        // Act accordingly based on payment status
        if ($payment->isPaid()) {
            $this->handlePaymentPaid($payment);
        } elseif (...) {
            // ...
        }
    }
}
