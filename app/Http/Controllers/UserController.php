<?php

namespace App\Http\Controllers;

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
use App\Http\Requests\UserRequest;

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
    public function show(User $user): Response
    {
        return Inertia::render('Panel/Users/Show', [
            'user' => $user->load('modules'), // Eager load the 'modules' relationship
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
            'initialModules' => Auth::user()->tenant->modules,
            'userLimitCount' => $this->maxUsersAllowed,
            'currentUserCount' => User::all()->count()
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
    public function store(UserRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if (User::all()->count() >= $this->maxUsersAllowed) {
            // Redirect back with an error message if the maximum limit is reached
            return redirect()->back()->with('error', 'You can not create more than 10 users.');
        }
        if (isset($validatedData['end_date_of_work'])) {
            $validatedData['status'] = 'inactive';
        } else {
            $validatedData['reason_of_leaving'] = null;
        }
        $user = User::create($validatedData);

        // Relate the user with selected modules
        $user->modules()->attach($validatedData['modules']);

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

        return Inertia::render('Panel/Users/Edit', [
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
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $validatedData = $request->validated();

        // Check if password is included and is not empty
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            // If password is not included, remove it from the $validatedData array
            unset($validatedData['password']);
        }
        if (isset($validatedData['end_date_of_work'])) {
            $validatedData['status'] = 'inactive';
        } else {
            $validatedData['reason_of_leaving'] = null;
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
