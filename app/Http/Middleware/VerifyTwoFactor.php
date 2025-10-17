<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTwoFactor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->two_factor_enabled) {
            if (!session('two_factor_verified')) {
                return redirect()->route('two-factor.verify');
            }
        }

        return $next($request);
    }
}
