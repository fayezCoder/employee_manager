<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect users who must change their temporary password.
 * Allows only the password change route and logout.
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->force_password_change) {
            $allowed = ['password.force-change', 'password.force-update', 'logout'];

            if (!$request->routeIs(...$allowed)) {
                return redirect()->route('password.force-change');
            }
        }

        return $next($request);
    }
}
