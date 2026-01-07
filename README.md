<p align="center">
  <img src="https://github.com/mollie/laravel-mollie/assets/7265703/5fce72ed-3fee-4645-b29a-18d97407fcfd" width="128" height="128"/>

</p>
<h1 align="center">Mollie for Laravel</h1>

![create new payment](https://github.com/mollie/laravel-mollie/assets/10154100/205b536f-51a3-4d1b-b2c9-69f02ba019fa)

Laravel-Mollie incorporates the [Mollie API](https://docs.mollie.com/) and [Mollie Connect](https://www.mollie.com/products/connect) into your [Laravel](https://laravel.com/) project.

Accepting [iDEAL](https://www.mollie.com/en/payments/ideal/), [Apple Pay](https://www.mollie.com/en/payments/apple-pay), [Bancontact/Mister Cash](https://www.mollie.com/en/payments/bancontact/), [SOFORT Banking](https://www.mollie.com/en/payments/sofort/), [Creditcard](https://www.mollie.com/en/payments/credit-card/), [SEPA Bank transfer](https://www.mollie.com/en/payments/bank-transfer/), [SEPA Direct debit](https://www.mollie.com/en/payments/direct-debit/), [PayPal](https://www.mollie.com/en/payments/paypal/), [Belfius Direct Net](https://www.mollie.com/en/payments/belfius/), [KBC/CBC](https://www.mollie.com/en/payments/kbc-cbc/), [paysafecard](https://www.mollie.com/en/payments/paysafecard/), [ING Home'Pay](https://www.mollie.com/en/payments/ing-homepay/), [Giftcards](https://www.mollie.com/en/payments/gift-cards/), [Giropay](https://www.mollie.com/en/payments/giropay/), [EPS](https://www.mollie.com/en/payments/eps/) and [Przelewy24](https://www.mollie.com/en/payments/przelewy24/) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.****

**Looking for a complete recurring billing solution?** Take a look at [Laravel Cashier for Mollie](https://www.cashiermollie.com) instead.

[![Build Status](https://github.com/mollie/laravel-mollie/workflows/tests/badge.svg)](https://github.com/mollie/laravel-mollie/actions)
[![Latest Stable Version](https://poser.pugx.org/mollie/laravel-mollie/v/stable)](https://packagist.org/packages/mollie/laravel-mollie)
[![Total Downloads](https://poser.pugx.org/mollie/laravel-mollie/downloads)](https://packagist.org/packages/mollie/laravel-mollie)

## **Requirements**

* Get yourself a free [Mollie account](https://my.mollie.com/dashboard/signup). No sign up costs.
* Now you're ready to use the Mollie API client in test mode.
* Follow [a few steps](https://www.mollie.com/dashboard/?modal=onboarding) to enable payment methods in live mode, and let us handle the rest.
* Up-to-date OpenSSL (or other SSL/TLS toolkit)
* PHP >= 8.2
* [Laravel](https://www.laravel.com) >= 11.0
* [Laravel Socialite](https://github.com/laravel/socialite) >= 5.0 (if you intend on using [Mollie Connect](https://docs.mollie.com/oauth/overview))

## Upgrading from v3.x?
To support the enhanced Mollie API v3, some breaking changes were introduced. Make sure to follow the instructions in the [upgrade guide](UPGRADE.md).

Fresh install? Continue with the installation guide below.

## Installation

Add Laravel-Mollie to your composer file via the `composer require` command:

```bash
composer require mollie/laravel-mollie
```

Or add it to `composer.json` manually:

```json
"require": {
    "mollie/laravel-mollie": "^4.0"
}
```

Laravel-Mollie's service providers will be automatically registered using Laravel's auto-discovery feature.

## Configuration

You'll only need to add the `MOLLIE_KEY` variable to your `.env` file.

```php
MOLLIE_KEY=test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

## Example usage

Here you can see an example of just how simple this package is to use.

### A payment using the Mollie API

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Laravel\Facades\Mollie;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

public function preparePayment()
{
    $request = new CreatePaymentRequest(
        description: 'Order #12345',
        amount: new Money('EUR', '10.00'),
        redirectUrl: route('order.success'),
        webhookUrl: route('webhooks.mollie'),
        metadata: [
            "order_id" => "12345",
            "customer_info" => [
                "name" => "John Doe",
                "email" => "john@example.com"
            ]
        ]
    );

    Mollie::send($request);

    // redirect customer to Mollie checkout page
    return redirect($payment->getCheckoutUrl(), 303);
}

/**
 * After the customer has completed the transaction,
 * you can fetch, check and process the payment.
 * This logic typically goes into the controller handling the inbound webhook request.
 * See the webhook docs in /docs and on mollie.com for more information.
 */
public function handleWebhookNotification(Request $request) {
    $paymentId = $request->input('id');

    $payment = Mollie::send(new GetPaymentRequest($paymentId));

    if ($payment->isPaid())
    {
        echo 'Payment received.';
        // Do your thing ...
    }
}
```

## Other examples

- [Process realtime status updates with a webhook](docs/webhook.md)
- [Recurring payments and direct charges](docs/recurring_and_direct_charge.md)
- [Using Mollie Connect with Laravel Socialite (OAuth)](docs/mollie_connect.md) (Leverage the Mollie platform for advanced payment use cases)

## Want to help us make our Laravel module even better?

Want to help us make our Laravel module even better? We take [pull requests](https://github.com/mollie/laravel-mollie/pulls?utf8=%E2%9C%93&q=is%3Apr), sure.
But how would you like to contribute to a technology oriented organization? Mollie is hiring developers and system engineers.
[Check out our vacancies](https://jobs.mollie.com/) or [get in touch](mailto:personeel@mollie.com).

## License

[The MIT License](LICENSE.md). Copyright (c) 2024, Mollie B.V.

## Support

Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com

* [More information about Mollie Connect](https://www.mollie.com/products/connect)
* [Documentation for the Mollie API](https://docs.mollie.com/)
* [Documentation for Mollie OAuth](https://docs.mollie.com/connect/getting-started)
