<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        //
    }
    public function view(User $user, User $model): bool
    {
        //
    }
    public function edit(User $user, User $model): bool
    {
        return $this->canModify($user, $model);
    }
    public function update(User $user, User $model): bool
    {
        return $this->canModify($user, $model);
    }
    public function delete(User $user, User $model): bool
    {
        return $this->canModify($user, $model);
    }
    private function canModify(User $user, User $model): bool
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
}
