<?php

namespace Mollie\Laravel\Commands;

use Illuminate\Console\Command;

class RevealWebhookPathCommand extends Command
{
    protected $signature = 'mollie:reveal-webhook-path';

    protected $description = 'Reveal the webhook path';

    public function handle()
    {
        $fullUrl = route('mollie.webhooks');

        $path = str($fullUrl)->after(config('app.url'));

        $this->info($path);
    }
}
