![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Laravel-Mollie [![Build Status](https://travis-ci.org/mollie/laravel-mollie.svg?branch=master)](https://travis-ci.org/mollie/laravel-mollie)

Laravel-Mollie incorporates [Mollie Connect](https://www.mollie.com/en/connect) and the [Mollie API](https://www.mollie.com/en/docs/overview) into your [Laravel](https://laravel.com/) or [Lumen](https://lumen.laravel.com/) project.

## Requirements

* [Laravel Socialite](https://github.com/laravel/socialite) (if you intend on using Mollie Connect)

## Installation

Add Laravel-Mollie to your composer file via the `composer require` command:

```bash
$ composer require mollie/laravel-mollie
```

Or add it to `composer.json` manually:

```json
"require": {
    "mollie/laravel-mollie": "~1.0"
}
```

Register the service provider by adding it to the `providers` key in `config/app.php`. Also register the facade by adding it to the `aliases` key in `config/app.php`.

```php
'providers' => [
    ...
    Mollie\Laravel\MollieServiceProvider::class,
],

'aliases' => [
    ...
    'Mollie' => Mollie\Laravel\Facades\Mollie::class,
]
```

Make sure that [Laravel Socialite](https://github.com/laravel/socialite) service provider and facade are also registered in your configuration files, if you intend on using Mollie Connect.

## Configuration

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish --provider="Mollie\Laravel\MollieServiceProvider"
```

This will create a `config/mollie.php` file in your app that you can modify to set your configuration.

## Usage

Here you can see an example of just how simple this package is to use.

### Mollie API

```php
$payment = Mollie::api()->payments()->create([
    "amount"      => 10.00,
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

## Possible problems

#### Webhook cannot be reached, because of CSRF protection

The `VerifyCsrfToken` middleware, which is included in the `web` middleware group by default, is the troublemaker if your webhook route is in the same middleware group in the `app/Http/routes.php` file.

```php
Route::post('mollie/webhook', function ($paymentId) { /** Your logic... */ });
```

You can exclude URIs from the CSRF protection in the `app/Http/Middleware/VerifyCsrfToken.php` file:

```php
/**
 * The URIs that should be excluded from CSRF verification.
 *
 * @var array
 */
protected $except = [
    'mollie/webhook'
];
```

If this solution does not work, open an [issue](https://github.com/mollie/laravel-mollie/issues) so we can assist you.

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
