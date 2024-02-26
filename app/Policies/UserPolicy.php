<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Check if the authenticated user's tenant id is equal to the model tenant id
        $tenantCheck = $user->tenant_id === $model->tenant_id;

        // Check if the authenticated user is an admin and the model has the role 'additional'
        $roleCheckAdmin = $user->role === 'admin' && ($model->role === 'additional');

        // Check if the authenticated user is a root and the model has the role 'admin' or 'additional'
        $roleCheckRoot = $user->role === 'root' && ($model->role === 'admin' || $model->role === 'additional');

        // Return true if any of the conditions are satisfied
        return $tenantCheck && ($roleCheckAdmin || $roleCheckRoot);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'root';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Check if the authenticated user's tenant id is equal to the model tenant id
        $tenantCheck = $user->tenant_id === $model->tenant_id;

        // Check if the authenticated user is an admin and the model has the role 'additional'
        $roleCheckAdmin = $user->role === 'admin' && ($model->role === 'additional');

        // Check if the authenticated user is a root and the model has the role 'admin' or 'additional'
        $roleCheckRoot = $user->role === 'root' && ($model->role === 'admin' || $model->role === 'additional');

        // Return true if any of the conditions are satisfied
        return $tenantCheck && ($roleCheckAdmin || $roleCheckRoot);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Check if the authenticated user's tenant id is equal to the model tenant id
        $tenantCheck = $user->tenant_id === $model->tenant_id;

        // Check if the authenticated user is an admin and the model has the role 'additional'
        $roleCheckAdmin = $user->role === 'admin' && ($model->role === 'additional');

        // Check if the authenticated user is a root and the model has the role 'admin' or 'additional'
        $roleCheckRoot = $user->role === 'root' && ($model->role === 'admin' || $model->role === 'additional');

        // Return true if any of the conditions are satisfied
        return $tenantCheck && ($roleCheckAdmin || $roleCheckRoot);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }
}
