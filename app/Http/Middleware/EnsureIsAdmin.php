<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     * Only users with role_id = 1 (Admin) may proceed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role_id !== 1) {
            abort(403, 'Access Denied — Admin only area.');
        }

        return $next($request);
    }
}
