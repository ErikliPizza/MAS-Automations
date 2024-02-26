<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and their status is not active or subscription is expired
        if (Auth::check()) {
            if (Auth::user()->status != 'active' || Auth::user()->tenant->status != 'active') {
                Auth::logout();
                return redirect('/login')->with('error', 'Your account is not active.');
            } else if (Auth::user()->tenant->expiry_time <= now()) {
                Auth::logout();
                return redirect('/login')->with('error', 'Your subscription is expired. Please renew your account.');
            }
        }

        return $next($request);
    }
}
