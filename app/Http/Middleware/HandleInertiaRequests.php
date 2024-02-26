<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'team' => [
                'users' => function () use ($request) {
                    // Fetch the current authenticated user
                    $user = $request->user();
                    if ($user) {
                        return User::all();
                    }
                    return null; // or an empty array, depending on what you want to return when there's no tenant ID or user
                }
            ]
            ,
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'info' => fn () => $request->session()->get('info'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'modules' => function() use ($request) {
                // Fetch the tenant ID from the session
                $tenantId = session('tenant_id');

                // Fetch the current authenticated user
                $user = $request->user();

                if ($tenantId && $user) {
                    if ($user->role === 'admin' || $user->role === 'root') {
                        // Fetch all modules for the tenant if the user is an admin
                        $tenant = Tenant::with('modules')->find($tenantId);
                        return $tenant->modules;
                    } else {
                        // Fetch only the modules assigned to the user if the user is not an admin
                        return $user->modules;
                    }
                }

                return null; // or an empty array, depending on what you want to return when there's no tenant ID or user
            }
        ];
    }
}
