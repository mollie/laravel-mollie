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

## Other Laravel packages by Mollie
Besides the Laravel-Mollie package, we're looking for other options to support you integrating Mollie in your Laravel applications.

### Explore Laravel Cashier support ("Laravel/Cashier-Mollie")
Laravel Cashier is a very popular package for easily adding recurring payments to your Laravel application. We are exploring whether it's possible for Laravel Cashier to support Mollie. You can join the discussion [here](https://github.com/mollie/laravel-mollie/issues/41).

### Explore Laravel Spark support
[Laravel Spark](https://spark.laravel.com/) is a commercial SAAS starter kit. By adding Cashier-Mollie support, new SAAS projects can be built rapidly on top of Mollie's subscription services.

Laravel Spark leverages Laravel Cashier for subscription billing.

When/If Cashier-Mollie is implemented, support for Laravel Spark would be a logical next topic for exploration.

### Explore Laravel Nova plugin options
[Laravel Nova](https://nova.laravel.com/) is a powerful Laravel and Vue based CMS (commercial license). Launch of this new CMS is scheduled August 2018. 

Adoption rate is expected to be high; [Sander](https://github.com/sandervanhooft) expects Nova is going to blow a hole in other Laravel and PHP CMSes' market shares.

A Laravel Nova plugin for Mollie could for example list and manage payments, and include a few dashboard cards.
