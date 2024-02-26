<?php

namespace App\Traits;
use Illuminate\Http\Request;

trait ModuleAssigner
{
    public function setModules(Request $request, $user)
    {
        $allowedRoles = ['admin', 'root'];

        // Check if the user has the specified roles
        if (in_array($request['role'], $allowedRoles)) {
            // Set modules array from Auth::user()->tenant->modules
            $modules = $user->tenant->modules->pluck('id')->toArray();

            // If you want to replace the existing modules in the request
            $request->merge(['modules' => $modules]);
        }

        // Return the modified request or any other relevant information
        return $request;
    }
}
