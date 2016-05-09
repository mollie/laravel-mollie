Laravel Mollie
==============

Laravel-Mollie incorporates [Mollie Connect](https://www.mollie.com/en/connect) and the [Mollie API](https://www.mollie.com/en/docs/overview) into your [Laravel](https://laravel.com/) or [Lumen](https://lumen.laravel.com/) project.

## Requirements

* [Laravel Socialite](https://github.com/laravel/socialite) (if you intend on using Mollie Connect)

## Installation

Add Laravel Mollie to your composer file via the `composer require` command:

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

Make sure that [Laravel Socialite](https://github.com/laravel/socialite) service provider and facade are also registered in your configuration files.

## Configuration

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
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

## License

[BSD (Berkeley Software Distribution) License](http://www.opensource.org/licenses/bsd-license.php). Copyright (c) 2016, Mollie B.V.

## Support

Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com — +31 20-612 88 55

* [More information about Mollie Connect](https://www.mollie.com/en/connect)
* [Documentation for the Mollie API](https://www.mollie.com/en/docs/overview)
* [Documentation for Mollie OAuth](https://www.mollie.com/en/docs/oauth/overview)
