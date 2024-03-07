![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Laravel-Mollie Roadmap

This roadmap lists all current and upcoming activity for the Laravel-Mollie package.

Please submit an [issue](https://github.com/mollie/laravel-mollie/issues) if you have a suggestion for Laravel-Mollie specific functionality.

## Planned for next major release

### Provide default webhook
The Laravel-Mollie package makes it easy to set up a new Mollie payment in your Laravel application. But right now, you'll need to implement the webhook yourself. We plan on providing a default webhook, which will trigger an Event when a payment status has been updated. This way, you'll only need to listen for the PaymentUpdatedEvent.

Another solution may be to provide a default overridable controller, like Cashier has. Or to implement both events and the controller.

### Switch to MIT license
Currently, the Laravel Mollie client has a *BSD 2-Clause "Simplified" License*. We're discussing switching to a *MIT* license, which is most common in Laravel packages.
