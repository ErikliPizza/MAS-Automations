<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Traits\ModuleAssigner;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\ModulesBelongToTenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    use ModuleAssigner;

    protected $maxUsersAllowed = 10;

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Panel/Users/Create', [
            'modules' => Auth::user()->tenant->modules,
            'limit' => $this->maxUsersAllowed,
            'existingUsers' => User::all()->count()
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Ensure the user has the privilege to make this operation
        $this->authorize('create', User::class);

        if (User::all()->count() >= $this->maxUsersAllowed) {
            // Redirect back with an error message if the maximum limit is reached
            return redirect()->back()->with('error', 'You can not create more than 10 users.');
        }

        // Set all available modules in this organization if the new user role is admin. Not crucially necessary but a good practice.
        $request = $this->setModules($request, Auth::user());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'phone' => 'nullable|regex:/^\d{10,15}$/',
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|string|in:admin,additional', // Adjust as necessary
            'modules' => ['required', 'array', 'min:1', new ModulesBelongToTenant()],
            'modules.*' => 'exists:modules,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'active',
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Relate the user with selected modules
        $user->modules()->attach($request->modules);

        event(new Registered($user));

        // Auth::login($user);

        return redirect()->back()->with('success', 'User has been registered successfully. They can now log in.');
    }
}
