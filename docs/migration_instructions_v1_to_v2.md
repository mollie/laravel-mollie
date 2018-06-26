![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Migrating from Laravel-Mollie v1.x to v2

### Step 1: Update composer dependencies

Update `composer.json` to match this:
```
"require": {
    "mollie/laravel-mollie": "^2.0"
}
```

Then run `composer update mollie/laravel-mollie`.

### Step 2: Reconfiguring the Mollie API key
Setting the api key has been simplified.

It now only requires you to set a single variable in your `.env` file. This is how you do it:

- If you have a `mollie.php` file in your `config/` directory, remove it.
- Add `MOLLIE_KEY=your_api_key_here` to your `.env` file. Use the test key for test mode, or the live key for live mode.
- You can now remove the `MOLLIE_TEST_MODE`, `MOLLIE_KEY_TEST` and `MOLLIE_KEY_LIVE` variables from the .env file.

### Step 3: Changed package methods
A few months ago Mollie launched the v2 API, along with an upgraded php core client.

The v2 release of this Laravel-Mollie package leverages the new features of the v2 API. This also means some breaking changes have been introduced in this package.

Some methods were removed:

- `Mollie::api()->permissions()`
- `Mollie::api()->organizations()`
- `Mollie::api()->issuers()`

These methods were renamed:

- `Mollie::api()->customerMandates()` into `Mollie::api()->mandates()`
- `Mollie::api()->customersPayments()` into `Mollie::api()->customerPayments()`
- `Mollie::api()->customerSubscriptions()` into `Mollie::api()->subscriptions()`
- `Mollie::api()->paymentsRefunds()` into `Mollie::api()->refunds()`

Also, this method was added:

- `Mollie::api()->invoices()`

### Step 4: Other changes

More breaking changes were introduced with the new Mollie API. Read about it here in the [official migration docs](https://docs.mollie.com/migrating-v1-to-v2).

## Stuck?
Feel free to open an [issue](https://github.com/mollie/laravel-mollie/issues).
