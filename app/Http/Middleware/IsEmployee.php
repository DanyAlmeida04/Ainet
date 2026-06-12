<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsEmployee
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Deny access to blocked users
        if ($user->blocked) {
            Auth::logout();
            abort(403, 'Conta bloqueada.');
        }

        if (! in_array($user->user_type, ['E', 'A'])) {
            abort(403);
        }

        return $next($request);
    }
}
