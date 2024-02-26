<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    protected $appointmentModuleId = 1; // The ID for the appointment module

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
    public function view(User $user, Appointment $appointment): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function ownModule(User $user)
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
     * Determine whether the user can create models.
     */
    public function create(User $user, Service $service)
    {
        // First, check if the user is an admin or root
        if (in_array($user->role, ['admin', 'root'])) {
            // Check if the tenant has the appointment module
            $hasModule = $user->tenant->modules()->where('modules.id', $this->appointmentModuleId)->exists();

            // Additionally, check if the service belongs to the user's tenant
            $serviceBelongsToTenant = $service->tenant_id === $user->tenant_id;

            // Both conditions must be true
            return $hasModule && $serviceBelongsToTenant;
        }
        // For 'additional' roles, check the user's modules
        elseif ($user->role === 'additional') {
            // Check if the user has the appointment module
            $hasModule = $user->modules()->where('modules.id', $this->appointmentModuleId)->exists();

            // Check if the service belongs to the user's tenant
            $serviceBelongsToTenant = $service->tenant_id === $user->tenant_id;

            // Both conditions must be true
            return $hasModule && $serviceBelongsToTenant;
        }

        // Default to false if none of the above conditions are met
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        //
    }
}
