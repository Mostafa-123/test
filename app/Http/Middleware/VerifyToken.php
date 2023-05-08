<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class VerifyToken
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$request->bearerToken()) {
            throw new AuthenticationException('Unauthorized');
        }

        if (auth($guard)->check()) {
            return $next($request);
        }

        throw new AuthenticationException('Unauthorized');
    }
}
