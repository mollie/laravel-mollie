<?php

declare(strict_types=1);

namespace Mollie\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mollie\Laravel\SignatureValidator;

class ValidatesWebhookSignatures
{
    public function __construct(
        private SignatureValidator $validator
    ) {}

    public function handle(Request $request, Closure $next)
    {
        if (! config('mollie.webhooks.enabled')) {
            return $next($request);
        }

        $this->validator->validate($request);

        return $next($request);
    }
}
