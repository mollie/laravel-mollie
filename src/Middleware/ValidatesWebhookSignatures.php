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
        $this->validator->validate($request);

        return $next($request);
    }
}
