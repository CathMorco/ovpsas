<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Update last_seen_at without messing up the 'updated_at' timestamp
            $user->timestamps = false;
            $user->last_seen_at = now();
            $user->save();
        }

        return $next($request);
    }
}