<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is logged in
        // 2. Allow access if they are either an Admin OR a Super Admin
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin())) {
            
            // If they are neither, show 403 Forbidden page
            abort(403, 'Access Denied: You do not have permission to view this page.');
        }

        return $next($request);
    }
}