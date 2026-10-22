<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ) {
        /*
        |--------------------------------------------------------------------------
        | User must be logged in
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | Account must be active
        |--------------------------------------------------------------------------
        */

        if (!auth()->user()->isActive()) {

            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your account is inactive.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Role Check
        |--------------------------------------------------------------------------
        */

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}