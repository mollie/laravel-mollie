![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Migrating from Laravel-Mollie v2.x to v3

## Update composer dependencies

Update `composer.json` to match this:

```json
"require": {
    "mollie/laravel-mollie": "^3.0"
}
```

Then run `composer update mollie/laravel-mollie`.

## Review Changes
### Updating Dependencies
Laravel-Mollie now requires PHP 8.1.0 or greater.

If you are using the mollie connect feature, make sure to checkout the upgrade instructions for [Laravel-Socialite](https://github.com/laravel/socialite/blob/5.x/UPGRADE.md)

#### Lumen support dropped
The Laravel team has added a note a while ago on the [Lumen Repository](https://github.com/laravel/lumen?tab=readme-ov-file) as well as the official [Lumen documentation](https://lumen.laravel.com/docs/master#installation) that they discourage starting a new project with Lumen. Therefore we dropped the Lumen support for this package.

### Removed Classes
In order to enhance maintainability the following classes were removed:

- `MollieApiWrapper`
- `MollieManager`

Instead the `MollieApiClient` is now directly resolved and provided through the container without any abstractions. This change means you can directly access the newest API features that are added to the underlying [mollie/mollie-api-php](https://github.com/mollie/mollie-api-php) client without having to wait on this repository being updated.

### Change in calling API endpoints
Previous versions of Laravel-Mollie forced you to call our endpoints through a static `api()` call. This has been removed to be in line with the mollie-api-php sdk. This also means that you can inject the `MollieApiClient` anywhere you like and it will already have the api key set for you.

```php
// before
Mollie::api()->payments()->create();

// now
Mollie::payments()->create();
```

Another small change we introduce is accessing endpoints through methods instead of properties. Previous versions would allow both. Going forward we will only support accessing endpoints through methods as this makes maintainability much easier.

```php
// before
$client->payments->create();

// now
$client->payments()->create();
```

### No more global helper function
The `mollie()` helper function was deleted. If you rely on the helper function, either consider switching to
- injecting or resolving the `MollieApiClient` from the container, or
- use the `Mollie` facade

If none of these are an option for you, you can create your own `helpers.php` file and insert the code for the `mollie()` function yourself.

```php
// app/helpers.php
<?php

use \Mollie\Api\MollieApiClient;

if (! function_exists('mollie')) {
    /**
     * @return MollieApiClient
     */
    function mollie()
    {
        return resolve(MollieApiClient::class);
    }
}
```

## Stuck?
Feel free to open an [issue](https://github.com/mollie/laravel-mollie/issues).
