<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Accepts one or more comma-separated roles, e.g.:
     *   middleware('role:admin')
     *   middleware('role:admin,kasir')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Force-logout deactivated accounts regardless of role
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
        }

        // Flatten roles: support both variadic args and comma-separated strings
        // e.g. middleware('role:admin,wasit') passes ["admin,wasit"] as $roles
        $allowedRoles = array_merge(...array_map(
            fn (string $role) => array_map('trim', explode(',', $role)),
            $roles
        ));

        if (! in_array($user->role, $allowedRoles, strict: true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
