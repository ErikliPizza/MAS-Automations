<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Tenant;
use App\Traits\ModuleAssigner;
use App\Models\User;
use App\Rules\ModulesBelongToTenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    use ModuleAssigner;

    public function index(): Response
    {
        return Inertia::render('Panel/Users/Index', [
            'users' => User::with('modules')->get()
        ]);
    }

    public function create(User $user)
    {
        $this->authorize('view', $user);

        return Inertia::render('Panel/Users/Show', [
            'user' => $user->load('modules'), // Eager load the 'modules' relationship
            'modules' => $user->tenant->modules,
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Ensure the user belongs to the same tenant as the current user and has the
        // privilege to make this operation based on this specific user's role
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

            if ($validatedData['role'] === 'admin' && Auth::user()->role === 'admin') {
                return redirect()->route("users.index")->with('success', 'User updated successfully!');
            } else {
                return redirect()->back()->with('success', 'User and modules updated successfully!');
            }
        } catch (\Exception $e) {
            // An error occurred; cancel the transaction...
            DB::rollback();
            // Capture the error message
            $errorMessage = $e->getMessage();

            // and return to the previous form with an error message
            return redirect()->back()->withInput()->withErrors(['error' => $errorMessage]);
        }
    }

    public function destroy(User $user)
    {
        // Ensure the user belongs to the same tenant as the current user and has the
        // privilege to make this operation based on this specific user's role
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route("users.index")->with('success', 'User deleted successfully!');
    }
}
