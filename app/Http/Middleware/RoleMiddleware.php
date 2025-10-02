<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $have = $user->role ?? null;
        if ($roles && !in_array($have, $roles, true)) {
            abort(403, 'Acesso negado (role).');
        }
        return $next($request);
    }
}
