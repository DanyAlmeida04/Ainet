<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNotBlocked
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->blocked) {
            Auth::logout();
            abort(403, 'Conta bloqueada. Contacte o administrador.');
        }

        return $next($request);
    }
}
