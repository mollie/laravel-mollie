![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Migrating from Laravel-Mollie v4.x to v5

## Update composer dependencies

Update `composer.json` to match this:

```json
"require": {
    "mollie/laravel-mollie": "^5.0"
}
```

Then run `composer update mollie/laravel-mollie`.

## Review Changes

### Mollie API PHP v4 Upgrade
Laravel-Mollie now requires mollie-api-php v4. The PHP and Laravel platform floor is unchanged from Laravel-Mollie v4: PHP 8.2 or greater and Laravel 11.0, 12.0, or 13.0.

The most relevant upstream changes are:

1. **Type constants are now enums**: classes under `Mollie\Api\Types` now expose string-backed enum cases such as `PaymentStatus::Paid`.
2. **Typed resources and value objects**: resource fields that were previously `stdClass` are now concrete value objects. Property names are unchanged, but code checking for `stdClass` may need to call `toArray()`.
3. **Readonly value objects**: `Money`, `Address`, `OrderLine`, and related data objects are readonly. Prefer factories or `Money::macro()` over subclassing.
4. **Generic `send()` return type**: static analysis can infer the returned resource from the request class. Manual `@var` casts around `Mollie::send(...)` can usually be removed.
5. **Typed SDK boundaries**: mollie-api-php v4 adds typed signatures. Whether an invalid scalar throws a `TypeError` or is coerced depends on the calling file's `strict_types` declaration.
6. **Omitted resource fields**: reading a typed field without a default now throws when the API response omits it. For fields that may be absent in partial responses, use `$payment->description ?? null` or `isset($payment->description)`. A missing required field signals a malformed response and should remain an error.

For full details on the mollie-api-php v4 changes, see the [official upgrade guide](https://github.com/mollie/mollie-api-php/blob/v4.0.0/UPGRADING.md).

### Money creation
You can keep using `new Money('EUR', '10.00')`, but v4 adds builder helpers that fit Laravel apps storing integer minor units:

```php
use Mollie\Api\Http\Data\Money;

Money::of('EUR')->fromString('10.00');
Money::of('EUR')->minorUnits(1000);
```

### Retry strategy configuration
The SDK still uses a linear retry strategy by default. Laravel-Mollie can now switch the SDK to mollie-api-php v4's exponential retry strategy from `config/mollie.php`:

```dotenv
MOLLIE_RETRY_STRATEGY=exponential
MOLLIE_RETRY_MAX_RETRIES=5
MOLLIE_RETRY_DELAY_MS=1000
MOLLIE_RETRY_EXPONENTIAL_MULTIPLIER=2.0
MOLLIE_RETRY_EXPONENTIAL_MAX_DELAY_MS=30000
MOLLIE_RETRY_EXPONENTIAL_JITTER=true
```

This retries temporary network failures and HTTP 429 responses. When Mollie sends a `Retry-After` header, the SDK uses that delay only when it is no greater than `MOLLIE_RETRY_EXPONENTIAL_MAX_DELAY_MS`. Otherwise, it does not retry that response.

### Webhook setup command
`mollie:setup-webhook` now includes profile webhook event types because mollie-api-php v4 ships event classes for profile lifecycle events.

### Testing
If your tests use `Mollie::fake()`, mollie-api-php v4 adds typed `MockResponse` factories for common resources:

```php
use Mollie\Api\Fake\MockResponse;

Mollie::fake([
    GetPaymentRequest::class => MockResponse::payment(
        id: 'tr_123',
        description: 'Order #123',
    ),
]);
```

Existing generic builders such as `MockResponse::resource(...)` and `MockResponse::list(...)` remain useful for resources without typed factories.

---

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

The `MollieSocialiteServiceProvider` is now also deferred and will only be loaded when `laravel/socialite` is installed in your application.

### Change in calling API endpoints
Earlier versions of Laravel-Mollie provided access to endpoints via both methods and properties. Moving forward, access to endpoints will be exclusively through properties:

```php
// before
Mollie::api()->payments()->create();

// now
Mollie::api()->payments->create();
```

Alternatively, mollie-api-php v3 now supports typed request objects which you can send directly using the facade:

```php
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Laravel\Facades\Mollie;

// You can send requests directly through the facade
$payment = Mollie::send(new CreatePaymentRequest(...));

// Or through the API client
$payment = Mollie::api()->send(new CreatePaymentRequest(...));
```

See the [mollie-api-php documentation](https://github.com/mollie/mollie-api-php/blob/master/docs/requests.md) for more details on typed request objects.

### Removed Components

#### MollieManager
The `Mollie\Laravel\MollieManager` class has been removed. This change has minor impact as the manager was primarily an internal component. If you were using it directly, switch to using the `Mollie` facade or resolve the `MollieApiClient` from the container instead:

```php
// Instead of MollieManager
use Mollie\Laravel\Facades\Mollie;

$client = Mollie::api();
```

#### Global Helper Function
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
