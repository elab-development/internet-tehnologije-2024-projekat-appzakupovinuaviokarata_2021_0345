<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            // 401 — neulogovan
            return response()->json(['message' => 'Nije autentifikovan.'], 401);
        }

        // Ako nisu prosleđene uloge
        if (!empty($roles) && !in_array($user->role, $roles, true)) {
            // 403 — nema dozvolu
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        return $next($request);
    }
}
