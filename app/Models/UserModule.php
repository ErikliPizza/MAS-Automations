<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserModule extends Pivot
{
    protected $table = 'user_module';

    use HasFactory, BelongsToTenant;

    protected static function booted()
    {
        static::saving(function ($userModule) {
            // Get the user and tenant
            $user = User::find($userModule->user_id);
            $tenant = Tenant::find($user->tenant_id);

            // Check if the tenant has the module
            $hasModule = $tenant->modules()->where('modules.id', $userModule->module_id)->exists();

            if (!$hasModule) {
                // Prevent saving and optionally throw an exception or return false
                throw new \Exception("The tenant does not have access to the specified module.");
                // Or return false to silently stop the operation
                // return false;
            }

            // If the tenant has the module, the saving will continue
        });
    }
}
