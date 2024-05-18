<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Tenant;
use App\Traits\ModuleAssigner;
use App\Models\User;
use App\Rules\ModulesBelongToTenant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    use ModuleAssigner; // assign all tenant modules to the user if the updated-created user role is admin or root
    protected $maxUsersAllowed = 10; // limit, may change later or move into as a database record

    /**
     * List users for tenant
     * Restrictions:
     *  * - Auth only
     *
     * Note: you may use a policy to add one more security layer to prevent showing other tenant's users here
     */
    public function index(): Response
    {
        return Inertia::render('Panel/Users/Index', [
            'users' => User::with('modules')->get()
        ]);
    }

    /**
     * List user for tenant
     * Restrictions:
     *  * - Auth only
     *
     * Note: you may use a policy to add one more security layer to prevent showing other tenant's user here
     */
    public function show(): Response
    {
        return Inertia::render('Panel/Users/Show', [
            'user' => User::with('modules')->get()
        ]);
    }

    /**
     * Show "add new user for tenant" form
     * Restrictions:
     *  * - Auth only
     *  * - Only accessible via 'canAccessAdminOrRoot' middleware (see routes/web.php)
     *  * - Middleware checks if user is an admin or root (see App\Http\Middleware\canAccessAdminOrRoot.php)
     * */
    public function create(): Response
    {
        return Inertia::render('Panel/Users/Create', [
            'modules' => Auth::user()->tenant->modules,
            'limit' => $this->maxUsersAllowed,
            'existingUsers' => User::all()->count()
        ]);
    }

    /**
     * Add new user for tenant
     * Restrictions:
     *  * - Auth only
     *  * - Only accessible via 'canAccessAdminOrRoot' middleware (see routes/web.php)
     *  * - Middleware checks if user is an admin or root (see App\Http\Middleware\canAccessAdminOrRoot.php)
     *
     * @throws ValidationException|AuthorizationException
     */
    public function store(Request $request): RedirectResponse
    {
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
            'role' => 'required|string|in:admin,additional',
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

        return redirect()->back()->with('success', 'User has been registered successfully. They can now log in.');
    }

    /**
     * Show "edit user for tenant" form
     * Restrictions:
     *  * - Auth only
     *  * - Only accessible via 'canAccessAdminOrRoot' middleware (see routes/web.php)
     *  * - Middleware checks if user is an admin or root (see App\Http\Middleware\canAccessAdminOrRoot.php)
     *  * - UserPolicy's edit function checks if the authenticated user's tenant id is equal to the model tenant id
     *  * - UserPolicy's edit function checks if the authenticated user's role is higher than the model's role (see Policies/UserPolicy - edit)
     *
     * @throws AuthorizationException
     */
    public function edit(User $user)
    {
        $this->authorize('edit', $user);

        return Inertia::render('Panel/Users/Show', [
            'user' => $user->load('modules'), // Eager load the 'modules' relationship
            'modules' => $user->tenant->modules,
        ]);
    }

    /**
     * Edit user for tenant
     * Restrictions:
     *  * - Auth only
     *  * - Only accessible via 'canAccessAdminOrRoot' middleware (see routes/web.php)
     *  * - Middleware checks if user is an admin or root (see App\Http\Middleware\canAccessAdminOrRoot.php)
     *  * - UserPolicy's update function checks if the authenticated user's tenant id is equal to the model tenant id
     *  * - UserPolicy's update function checks if the authenticated user's role is higher than the model's role (see Policies/UserPolicy - update)
     *
     * @throws AuthorizationException
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        // Set all available modules in this organization if the new user role is admin. Not crucially necessary but a good practice.
        $request = $this->setModules($request, $user);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|regex:/^\d{10,15}$/',
            'role' => [
                'required',
                Rule::in(['additional', 'admin']),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'modules' => ['required', 'array', 'min:1', new ModulesBelongToTenant()],
            'modules.*' => ['required', 'exists:modules,id'],
        ]);

        // Check if password is included and is not empty
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            $user->update($validatedData);
            $user->modules()->sync($validatedData['modules']);

            // Commit the transaction
            DB::commit();

            return redirect()->route('user.profile', ['user' => $user])->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            // An error occurred; cancel the transaction...
            DB::rollback();
            // Capture the error message
            $errorMessage = $e->getMessage();

            // and return to the previous form with an error message
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Delete user for tenant
     * Restrictions:
     *  * - Auth only
     *  * - Only accessible via 'canAccessAdminOrRoot' middleware (see routes/web.php)
     *  * - Middleware checks if user is an admin or root (see App\Http\Middleware\canAccessAdminOrRoot.php)
     *  * - UserPolicy's delete function checks if the authenticated user's tenant id is equal to the model tenant id
     *  * - UserPolicy's delete function checks if the authenticated user's role is higher than the model's role (see Policies/UserPolicy - delete)
     *
     * @throws AuthorizationException
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route("users.index")->with('success', 'User deleted successfully!');
    }
}
