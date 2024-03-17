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
In order to enhance maintainability the following class was removed:

- `MollieApiWrapper`

Instead the `MollieApiClient` is now directly resolved and provided through the container without any abstractions. This change means you can directly access the newest API features that are added to the underlying [mollie/mollie-api-php](https://github.com/mollie/mollie-api-php) client without having to wait on this repository being updated.

### Change in calling API endpoints
Earlier versions of Laravel-Mollie provided access to endpoints via both methods and properties. Moving forward, access to endpoints will be exclusively through properties, aligning with the practices of the mollie-api-php SDK.****

```php
// before
Mollie::api()->payments()->create();

// now
Mollie::api()->payments->create();
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
