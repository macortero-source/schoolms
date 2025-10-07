<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles  Roles allowed to access the route
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Make sure the user is authenticated
        if (! $request->user()) {
            abort(403, 'Unauthorized'); 
        }

        // Check if user's role is allowed
         if (!in_array($request->user()->role, $roles)) {
        abort(403, 'Unauthorized');
    }

        // Allow request to continue
        return $next($request);
    }
}
