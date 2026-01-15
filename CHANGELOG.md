# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

<!--  -->
<!-- ## [Unreleased] -->
<!-- ### Added -->
<!-- ### Changed -->
<!-- ### Removed -->
## v4.0.1 - 2026-01-15

### What's Changed

* fix: remove DeferrableProvider implementation by @Naoray in https://github.com/mollie/laravel-mollie/pull/256

**Full Changelog**: https://github.com/mollie/laravel-mollie/compare/v4.0.0...v4.0.1

## 4.0.0 2026-01-08

### Added

- Support for mollie-api-php v3 with typed request objects - you can now use `Mollie::send(new CreatePaymentRequest(...))`.
- `ValidatesWebhookSignatures` middleware for validating incoming webhook signatures.
- `HandleIncomingWebhook` controller for processing webhook requests.
- `WebhookDispatcher` service that dispatches webhook events (extensible for custom handling strategies).
- `SetupWebhookCommand` to automatically configure webhooks via the Mollie API or guide manual setup.
- Webhook route file (`routes/webhook.php`) for handling incoming webhooks.
- Applied updated Adapter Contract to `MollieLaravelHttpClientAdapter`.

### Changed

- Minimum PHP version is now 8.2.0.
- Only Laravel 11.x and 12.x are supported.
- Upgraded to mollie-api-php v3 which includes several breaking changes (see [mollie-api-php upgrade guide](https://github.com/mollie/mollie-api-php/blob/master/UPGRADING.md)).
- Endpoint access changed from methods to properties (e.g., `payments()` becomes `payments`).
- Metadata in API request payloads now only accepts arrays (not strings or objects).
- Main `MollieServiceProvider` is now deferred for improved performance.
- `MollieSocialiteServiceProvider` is now deferred and only loaded when `laravel/socialite` is installed.
- Separated Socialite integration into dedicated service provider (`MollieSocialiteServiceProvider`).
- Test on PHP 8.4 and 8.5 by @jnoordsij in https://github.com/mollie/laravel-mollie/pull/252
- fix version vars by @sandervanhooft in https://github.com/mollie/laravel-mollie/pull/254

### Removed

- `Mollie\Laravel\MollieManager` class (use `Mollie` facade instead).
- `mollie()` global helper function (use `Mollie::api()` facade or dependency injection instead).
- Support for Laravel 10.x and earlier.
- Support for PHP 8.1 and earlier.

**Full Changelog**: https://github.com/mollie/laravel-mollie/compare/v3.1.0...v4.0.0

This release leverages the V2 mollie-api-php client.
