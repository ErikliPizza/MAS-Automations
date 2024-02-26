<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    protected $appointmentModuleId = 1; // The ID for the appointment module

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Check if the user is an admin or root, and if so, check the tenant's modules
        if (in_array($user->role, ['admin', 'root'])) {
            return $user->tenant->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }
        // For 'additional' roles, check the user's modules as before
        elseif ($user->role === 'additional') {
            return $user->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }

        // Default to false if none of the above conditions are met
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        // Check if the user's tenant_id matches the service's tenant_id
        $tenantCheck = $user->tenant_id === $service->tenant_id;

        // Check if the user is an admin or root, and if so, check the tenant's modules
        if (in_array($user->role, ['admin', 'root'])) {
            return $tenantCheck && $user->tenant->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }
        // For 'additional' roles, check the user's modules as before
        elseif ($user->role === 'additional') {
            return $tenantCheck && $user->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }

        // Default to false if none of the above conditions are met
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Check if the user is an admin or root, and if so, check the tenant's modules
        if (in_array($user->role, ['admin', 'root'])) {
            return $user->tenant->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }
        // For 'additional' roles, check the user's modules as before
        elseif ($user->role === 'additional') {
            return $user->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }

        // Default to false if none of the above conditions are met
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        // Check if the user's tenant_id matches the service's tenant_id
        $tenantCheck = $user->tenant_id === $service->tenant_id;

        // Check if the user is an admin or root, and if so, check the tenant's modules
        if (in_array($user->role, ['admin', 'root'])) {
            return $tenantCheck && $user->tenant->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }
        // For 'additional' roles, check the user's modules as before
        elseif ($user->role === 'additional') {
            return $tenantCheck && $user->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }

        // Default to false if none of the above conditions are met
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        // Check if the user's tenant_id matches the service's tenant_id
        $tenantCheck = $user->tenant_id === $service->tenant_id;

        // Check if the user is an admin or root, and if so, check the tenant's modules
        if (in_array($user->role, ['admin', 'root'])) {
            return $tenantCheck && $user->tenant->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }
        // For 'additional' roles, check the user's modules as before
        elseif ($user->role === 'additional') {
            return $tenantCheck && $user->modules()->where('modules.id', $this->appointmentModuleId)->exists();
        }

        // Default to false if none of the above conditions are met
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        //
    }
}
