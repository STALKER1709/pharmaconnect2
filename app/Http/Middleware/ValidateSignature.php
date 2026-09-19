<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignature
{
    public function handle(Request $request, Closure $next, string ...$args): Response
    {
        if (! $request->hasValidSignatureWhileIgnoring($args)) {
            throw new InvalidSignatureException;
        }

        return $next($request);
    }
}
