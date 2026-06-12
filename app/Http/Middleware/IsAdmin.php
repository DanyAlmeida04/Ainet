<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IsAdmin
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
            Log::warning('IsAdmin middleware blocked request: user blocked', ['id' => $user->id ?? null, 'user_type' => $user->user_type ?? null]);
            abort(403, 'Conta bloqueada.');
        }

        // Accept several possible admin markers for robustness
        $type = strtoupper((string) ($user->user_type ?? ''));
        if (! in_array($type, ['A', 'ADMIN'])) {
            Log::warning('IsAdmin middleware denied access: not admin', ['id' => $user->id ?? null, 'user_type' => $user->user_type ?? null]);
            abort(403, 'Ação não autorizada. Apenas administradores podem aceder.');
        }

        return $next($request);
    }
}
