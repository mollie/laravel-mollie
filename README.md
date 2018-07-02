![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Laravel-Mollie

Laravel-Mollie incorporates the [Mollie API](https://www.mollie.com/en/docs/overview) and [Mollie Connect](https://www.mollie.com/en/connect) into your [Laravel](https://laravel.com/) or [Lumen](https://lumen.laravel.com/) project.

Accepting [iDEAL](https://www.mollie.com/en/payments/ideal/), [Bancontact/Mister Cash](https://www.mollie.com/en/payments/bancontact/), [SOFORT Banking](https://www.mollie.com/en/payments/sofort/), [Creditcard](https://www.mollie.com/en/payments/credit-card/), [SEPA Bank transfer](https://www.mollie.com/en/payments/bank-transfer/), [SEPA Direct debit](https://www.mollie.com/en/payments/direct-debit/), [Bitcoin](https://www.mollie.com/en/payments/bitcoin/), [PayPal](https://www.mollie.com/en/payments/paypal/), [Belfius Direct Net](https://www.mollie.com/en/payments/belfius/), [KBC/CBC](https://www.mollie.com/en/payments/kbc-cbc/), [paysafecard](https://www.mollie.com/en/payments/paysafecard/), [ING Home'Pay](https://www.mollie.com/en/payments/ing-homepay/), [Giftcards](https://www.mollie.com/en/payments/gift-cards/), [Giropay](https://www.mollie.com/en/payments/giropay/) and [EPS](https://www.mollie.com/en/payments/eps/) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.

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
$ composer require mollie/laravel-mollie:2.0.*
```

Or add it to `composer.json` manually:

```json
"require": {
    "mollie/laravel-mollie": "^2.0"
}
```

Laravel-Mollie's service providers will be automatically registered using Laravel's auto-discovery feature.

## Configuration

You'll only need to add the `MOLLIE_KEY` variable to your `.env` file.

```php
MOLLIE_KEY=test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Mollie Connect with Laravel Socialite

If you intend on using [Mollie Connect](https://docs.mollie.com/oauth/overview), update `config/services.php` by adding this to the array:

```php
'mollie' => [
    'client_id' => env('MOLLIE_CLIENT_ID', 'app_xxx'),
    'client_secret' => env('MOLLIE_CLIENT_SECRET'),
    'redirect' => env('MOLLIE_REDIRECT_URI'),
],
```

Then add the corresponding credentials (`MOLLIE_CLIENT_ID`, `MOLLIE_CLIENT_SECRET`, `MOLLIE_REDIRECT_URI`) to your `.env` file.

To make sure Laravel Socialite can actually find the Mollie driver, use the following code snippet and paste it in the `boot()` method of your `AppServiceProvider.php`.

```php
Socialite::extend('mollie', function ($app) {
    $config = $app['config']['services.mollie'];

    return Socialite::buildProvider('Mollie\Laravel\MollieConnectProvider', $config);
});
```

## Usage

Here you can see an example of just how simple this package is to use.

### Mollie API

```php
$payment = Mollie::api()->payments()->create([
    'amount' => [
        'currency' => 'EUR',
        'value' => '10.00', // You must send the correct number of decimals, thus we enforce the use of strings
    ],
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
]);

$payment = Mollie::api()->payments()->get($payment->id);

if ($payment->isPaid())
{
    echo "Payment received.";
}
```

### Mollie Connect with Laravel Socialite

```php
Route::get('login', function () {
    return Socialite::with('mollie')
        ->scopes(['profiles.read']) // Additional permission: profiles.read
        ->redirect();
});

Route::get('login_callback', function () {
    $user = Socialite::with('mollie')->user();

    Mollie::api()->setAccessToken($user->token);

    return Mollie::api()->profiles()->all(); // Retrieve all payment profiles available on the obtained Mollie account
});
```

## Mollie Recurring

Here you can see an example of how easy it is to use [Mollie recurring](https://www.mollie.com/nl/docs/recurring) payments.

### Create a customer

First of all you need to [create a new customer](https://www.mollie.com/nl/docs/recurring#first-payment) (step 1), this is pretty straight forward

```php
$customer = Mollie::api()->customers()->create([
    "name"  => "John Doe",
    "email" => "john@doe.com",
]);
```

### Initial Payment

After creating the user, you can [start a payment](https://www.mollie.com/nl/docs/recurring#first-payment) (step 3), it's important to set `sequenceType` to `first`, this will generate a mandate on Mollie's end that can be used to do direct charges. Without setting the `method` the payment screen of Mollie will display your methods that support recurring payments.

```php
$payment = Mollie::api()->payments()->create([
    'amount' => [
        'currency' => 'EUR',
        'value' => '25.00', // You must send the correct number of decimals, thus we enforce the use of strings
    ],
    'customerId'    => $customer->id,
    'sequenceType' => 'first',
    'description'   => 'My Initial Payment',
    'redirectUrl'   => 'https://domain.com/return',
]);
```

### Direct Charge

After doing the initial payment, you may [charge the users card/account directly](https://www.mollie.com/nl/docs/recurring#on-demand). Make sure there's a valid mandate connected to the customer. In case there are multiple mandates at least one should have `status` set to `valid`. Checking mandates is easy:

```php
$mandates = Mollie::api()->mandates()->listFor($customer);
```

If any of the mandates is valid, charging the user is a piece of cake. Make sure `sequenceType` is set to `recurring`.


```php
 $payment = Mollie::api()->payments()->create([
    'amount' => [
        'currency' => 'EUR',
        'value' => '25.00', // You must send the correct number of decimals, thus we enforce the use of strings
    ],
    'customerId'    => $customer->id,
    'sequenceType' => 'recurring',
    'description'   => 'Direct Charge',
]);
```

Like any other payment, Mollie will call your webhook to register the payment status so don't forget to save the transaction id to your database.


## Possible problems

#### Webhook cannot be reached, because of CSRF protection

The `VerifyCsrfToken` middleware, which is included in the `web` middleware group by default, is the troublemaker if your webhook route is in the same middleware group in the `app/Http/routes.php` file.

```php
Route::post('webhooks/mollie', function ($paymentId) { /** Your logic... */ });
```

You can exclude URIs from the CSRF protection in the `app/Http/Middleware/VerifyCsrfToken.php` file:

```php
/**
 * The URIs that should be excluded from CSRF verification.
 *
 * @var array
 */
protected $except = [
    'webhooks/mollie'
];
```

If this solution does not work, open an [issue](https://github.com/mollie/laravel-mollie/issues) so we can assist you.

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
