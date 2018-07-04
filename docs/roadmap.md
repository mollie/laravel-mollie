![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Laravel-Mollie Roadmap

This roadmap lists all current and upcoming activity for the Laravel-Mollie package.

Please submit an [issue](https://github.com/mollie/laravel-mollie/issues) if you have a suggestion for Laravel-Mollie specific functionality.

## [ ] Research Laravel Cashier support
Laravel Cashier is a very popular package for easily adding recurring payments to your Laravel application. We are researching whether it's possible for Laravel Cashier to support Mollie. You can join the discussion [here](https://github.com/mollie/laravel-mollie/issues/41).

## [ ] Default webhook
The Laravel-Mollie package makes it easy to set up a new Mollie payment in your Laravel application. But right now, you'll need to implement the webhook yourself. We plan on providing a default webhook, which will trigger an Event when a payment status has been updated. This way, you'll only need to listen for the PaymentUpdatedEvent.
