# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).
<!--  -->
<!-- ## [Unreleased] -->
<!-- ### Added -->
<!-- ### Changed -->
<!-- ### Removed -->

## 2.5.0 2019-03-03
### Added
- A method to record the User Agent (UA) string when calling the Mollie API: `mollie()->addVersionString()`. If you're using this package within another package, this one is for you.

## 2.4.1 - 2018-12-02
### Changed
- Laravel-mollie now depends on [mollie-api-php](https://github.com/mollie/mollie-api-php) version 2.2.0 and up.

## 2.4.0 - 2018-11-22
### Added
- You can now list chargebacks across all payments on the payment profile using `mollie()->chargebacks()->page()`.

## 2.3.1 - 2018-11-10
### Changed
- Bug fix for using Socialite with Laravel Mollie. See [issue #73](https://github.com/mollie/laravel-mollie/issues/73) for more information.

## 2.3.0 - 2018-10-04
### Added
- You can now process your Orders, Shipments and Captures using the latest Laravel Mollie client. See [Mollie's guides](https://docs.mollie.com/orders/overview) for more information about these brand new features.

### Changed
- Bumped mollie core client dependency to from `^2.0` to `^2.1`.

## 2.2.0 - 2018-09-18
### Added
- Added support for the Organizations and Permissions endpoints. Advanced (OAuth) users, this one is for you.

## 2.1.0 - 2018-07-30
### Added
- Introducing the global helper method `mollie()`. A convenient shortcut you can use anywhere in your Laravel application instead of `Mollie::api()`.

## 2.0.0 - 2018-07-09
Version 2.0.0 is here! See the [migration instructions](docs/migration_instructions_v1_to_v2.md) on how to upgrade from v1.

This release leverages the V2 mollie-api-php client.
