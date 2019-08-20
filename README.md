<p align="center">
  <img src="https://info.mollie.com/hubfs/github/laravel/logo.png" width="128" height="128"/>
</p>
<h1 align="center">Mollie for Laravel</h1>

<img src="https://info.mollie.com/hubfs/github/laravel/editor.png" />

Laravel-Mollie incorporates the [Mollie API](https://www.mollie.com/en/docs/overview) and [Mollie Connect](https://www.mollie.com/en/connect) into your [Laravel](https://laravel.com/) or [Lumen](https://lumen.laravel.com/) project.

Accepting [iDEAL](https://www.mollie.com/en/payments/ideal/), [Apple Pay](https://www.mollie.com/en/payments/apple-pay), [Bancontact/Mister Cash](https://www.mollie.com/en/payments/bancontact/), [SOFORT Banking](https://www.mollie.com/en/payments/sofort/), [Creditcard](https://www.mollie.com/en/payments/credit-card/), [SEPA Bank transfer](https://www.mollie.com/en/payments/bank-transfer/), [SEPA Direct debit](https://www.mollie.com/en/payments/direct-debit/), [PayPal](https://www.mollie.com/en/payments/paypal/), [Belfius Direct Net](https://www.mollie.com/en/payments/belfius/), [KBC/CBC](https://www.mollie.com/en/payments/kbc-cbc/), [paysafecard](https://www.mollie.com/en/payments/paysafecard/), [ING Home'Pay](https://www.mollie.com/en/payments/ing-homepay/), [Giftcards](https://www.mollie.com/en/payments/gift-cards/), [Giropay](https://www.mollie.com/en/payments/giropay/), [EPS](https://www.mollie.com/en/payments/eps/) and [Przelewy24](https://www.mollie.com/en/payments/przelewy24/) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.

**Looking for a complete recurring billing solution?** Take a look at [laravel/cashier-mollie](https://www.github.com/laravel/cashier-mollie) instead.

[![Build Status](https://travis-ci.org/mollie/laravel-mollie.png)](https://travis-ci.org/mollie/laravel-mollie)
[![Latest Stable Version](https://poser.pugx.org/mollie/laravel-mollie/v/stable)](https://packagist.org/packages/mollie/laravel-mollie)
[![Total Downloads](https://poser.pugx.org/mollie/laravel-mollie/downloads)](https://packagist.org/packages/mollie/laravel-mollie)

## Requirements

* Get yourself a free [Mollie account](https://www.mollie.com/signup). No sign up costs.
* Now you're ready to use the Mollie API client in test mode.
* Follow [a few steps](https://www.mollie.com/dashboard/?modal=onboarding) to enable payment methods in live mode, and let us handle the rest.
* Up-to-date OpenSSL (or other SSL/TLS toolkit)
* PHP >= 7.0
* [Laravel](https://www.laravel.com) (or [Lumen](https://lumen.laravel.com)) >= 5.5
* [Laravel Socialite](https://github.com/laravel/socialite) >= 3.0 (if you intend on using [Mollie Connect](https://docs.mollie.com/oauth/overview))

## Upgrading from v1.x?
To support the enhanced Mollie API, some breaking changes were introduced. Make sure to follow the instructions in the [package migration guide](docs/migration_instructions_v1_to_v2.md).

Not ready to upgrade? The Laravel-Mollie client v1 will remain supported for now.

Fresh install? Continue with the installation guide below.

## Installation

Add Laravel-Mollie to your composer file via the `composer require` command:

```bash
$ composer require mollie/laravel-mollie:^2.0
```

Or add it to `composer.json` manually:

```json
"require": {
    "mollie/laravel-mollie": "^2.0"
}
```

Laravel-Mollie's service providers will be automatically registered using Laravel's auto-discovery feature.

Note: For Lumen you have to add the Mollie facade and service provider manually to: `bootstrap/app.php` :
```php
$app->withFacades(true, ['Mollie\Laravel\Facades\Mollie' => 'Mollie']);

$app->register(Mollie\Laravel\MollieServiceProvider::class);
```

## Configuration

You'll only need to add the `MOLLIE_KEY` variable to your `.env` file.

```php
MOLLIE_KEY=test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

## Example usage

Here you can see an example of just how simple this package is to use.

### A payment using the Mollie API

```php
use Mollie\Laravel\Facades\Mollie;

public function preparePayment()
{
    $payment = Mollie::api()->payments()->create([
    'amount' => [
        'currency' => 'EUR',
        'value' => '10.00', // You must send the correct number of decimals, thus we enforce the use of strings
    ],
    'description' => 'My first API payment',
    'webhookUrl' => route('webhooks.mollie'),
    'redirectUrl' => route('order.success'),
    ]);

    $payment = Mollie::api()->payments()->get($payment->id);

    // redirect customer to Mollie checkout page
    return redirect($payment->getCheckoutUrl(), 303);
}

/**
 * After the customer has completed the transaction,
 * you can fetch, check and process the payment.
 * (See the webhook docs for more information.)
 */
if ($payment->isPaid())
{
    echo 'Payment received.';
    // Do your thing ...
}
```

### Global helper method
For your convencience we've added the global `mollie()` helper function. It's an easy shortcut to the `Mollie::api()` facade accessor.

```php
// Using facade accessor
$payment = \Mollie\Laravel\Facades\Mollie::api()->payments()->get($payment_id);

// Using global helper function
$payment = mollie()->payments()->get($payment_id);
```

## Other examples

- [Process realtime status updates with a webhook](docs/webhook.md)
- [Recurring payments and direct charges](docs/recurring_and_direct_charge.md)
- [Using Mollie Connect with Laravel Socialite (OAuth)](docs/mollie_connect.md) (Leverage the Mollie platform for advanced payment use cases)

## Roadmap

You can find the latest development roadmap for this package [here](docs/roadmap.md). Feel free to open an [issue](https://github.com/mollie/laravel-mollie/issues) if you have a feature request.

## Want to help us make our Laravel module even better?

Want to help us make our Laravel module even better? We take [pull requests](https://github.com/mollie/laravel-mollie/pulls?utf8=%E2%9C%93&q=is%3Apr), sure.
But how would you like to contribute to a [technology oriented organization](https://www.mollie.com/nl/blog/post/werken-bij-mollie-als-developer/)? Mollie is hiring developers and system engineers.
[Check out our vacancies](https://www.mollie.com/nl/jobs) or [get in touch](mailto:personeel@mollie.com).

## License

[BSD (Berkeley Software Distribution) License](http://www.opensource.org/licenses/bsd-license.php). Copyright (c) 2016, Mollie B.V.

## Support

Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com — +31 20-612 88 55

* [More information about Mollie Connect](https://www.mollie.com/en/connect)
* [Documentation for the Mollie API](https://www.mollie.com/en/docs/overview)
* [Documentation for Mollie OAuth](https://www.mollie.com/en/docs/oauth/overview)
