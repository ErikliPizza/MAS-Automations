<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanAccessAdminOrRoot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = ['admin', 'root'];
        if (auth()->check() && in_array(auth()->user()->role, $allowedRoles)) {
            return $next($request);
        }

        // Redirect to login page or a designated page if the user is not an admin
        return response('Unauthorized.', 403);
    }
}
