<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Safeguard: Check if user exists AND is Super Admin
        if ($request->user() && $request->user()->isSuperAdmin()) {
            return $next($request);
        }

        // If not a Super Admin, redirect back with an error
        return redirect('/dashboard')->with('error', 'Unauthorized access. Super Admin privileges required.');
    }
}