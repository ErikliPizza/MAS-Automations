<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckAppointment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user(); // Get the authenticated user

        // Check if the user exists and has a role
        if ($user && isset($user->role)) {
            $moduleAllowed = false; // Default to false

            // Check for 'admin' or 'root' roles to look at tenant's modules
            if (in_array($user->role, ['admin', 'root'])) {
                $tenantId = $user->tenant_id; // Assuming the user model has a tenant_id column

                // Assuming there's a tenant_module table for tenant's modules
                $moduleAllowed = DB::table('tenant_module')
                    ->where('tenant_id', $tenantId)
                    ->where('module_id', 1) // '1' for the appointment module
                    ->exists();
            }
            // For 'additional' roles, check the user's modules
            elseif ($user->role === 'additional') {
                $moduleAllowed = DB::table('user_module')
                    ->where('user_id', $user->id)
                    ->where('module_id', 1) // '1' for the appointment module
                    ->exists();
            }

            // If the user does not have the module permission
            if (!$moduleAllowed) {
                // Redirect or show an error if the module is not allowed
                return redirect()->back()->with('error', 'You do not have permission to access this resource.');
            }
        } else {
            // Redirect or handle the case where there's no authenticated user
            return redirect()->route('login')->with('error', 'You must be logged in to access this resource.');
        }

        return $next($request);
    }
}
