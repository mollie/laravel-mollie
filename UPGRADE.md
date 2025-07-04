![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Migrating from Laravel-Mollie v3.x to v4

## Update composer dependencies

Update `composer.json` to match this:

```json
"require": {
    "mollie/laravel-mollie": "^4.0"
}
```

Then run `composer update mollie/laravel-mollie`.

## Review Changes
### Updating Dependencies
Laravel-Mollie now requires PHP 8.2.0 or greater and supports Laravel 11.0 and 12.0 only. It leverages mollie-api-php version 3, which includes several breaking changes.

### Mollie API PHP v3 Upgrade
This version upgrades to mollie-api-php v3, which includes several breaking changes:

1. **Metadata Type Restriction**: In v3, metadata in request payloads is restricted to only accept arrays (not strings or objects).
2. **Class & Method Renames**: Several endpoint classes and methods have been renamed.
3. **Streamlined Constants**: Redundant prefixes have been removed for a cleaner API.
4. **Test Mode Handling**: Automatic detection with API keys and explicit parameter for organization credentials.
5. **Modern HTTP Handling**: PSR-18 support and typed request objects.

For full details on the mollie-api-php v3 changes, see the [official upgrade guide](https://github.com/mollie/mollie-api-php/blob/master/UPGRADING.md).

### Socialite Integration Changes
The Socialite integration has been moved to a dedicated service provider (`MollieSocialiteServiceProvider`) which is automatically registered alongside the main provider. This allows the main `MollieServiceProvider` to be deferred, improving application performance when the Mollie API is not being used.

### Deferred Service Provider
The main `MollieServiceProvider` is now deferrable, which means it will only be loaded when the Mollie API is actually used in your application. This can improve application performance.

If you're using the Socialite integration, the `MollieSocialiteServiceProvider` will still be loaded on every request to ensure the Socialite driver is properly registered.

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
- use the `Mollie\Laravel\Facades\Mollie::api()` facade

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
Feel free to open an [issue](https://github.com/mollie/laravel-mollie/issues) or come say hi in the [Mollie Community Discord](https://discord.gg/mollie).
