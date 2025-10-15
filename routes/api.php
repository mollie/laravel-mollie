<?php

use Illuminate\Support\Facades\Route;
use Mollie\Laravel\Controllers\HandleIncomingWebhook;

if (config('mollie.webhooks.enabled')) {
    Route::middleware(config('mollie.webhooks.middleware'))
        ->prefix(config('mollie.webhooks.prefix'))
        ->post(config('mollie.webhooks.path'), HandleIncomingWebhook::class)
        ->name('mollie.webhooks');
}
