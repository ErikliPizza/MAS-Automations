<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected $t = true;

    // create user
    /** @test
     * Tenant has the module
     * Auth user has not the module -somehow a bug occurred
     * - But still you can access and create service because you are admin, and you should have all the modules in your tenant
     * */
    public function test_tenant_has_module_user_has_not_with_role_of_admin_and_root()
    {
        $tenant = Tenant::factory()->create();
        Module::factory(5)->create();
        $this->performServiceCreateTest('admin', '1' ,'2', $tenant)->assertSessionHas('success');
        $this->performServiceCreateTest('root', '1' , '2', $tenant)->assertSessionHas('success');
        $this->performServiceCreateTest('additional', '1' , '2', $tenant)->assertSessionHas('error');

        $this->performServiceCreateTest('admin', '2' ,'2', $tenant)->assertSessionHas('error');
        $this->performServiceCreateTest('root', '2' , '2', $tenant)->assertSessionHas('error');
        $this->performServiceCreateTest('additional', '2' , '2', $tenant)->assertSessionHas('error');

        $this->performServiceCreateTest('admin', '2' ,'1', $tenant)->assertSessionHas('success');
        $this->performServiceCreateTest('root', '2' , '1', $tenant)->assertSessionHas('success');
        $this->performServiceCreateTest('additional', '2' , '1', $tenant)->assertSessionHas('success');
    }

    private function performServiceCreateTest($role, $tenantModuleId, $userModuleId, $tenant)
    {
        $tenant->modules()->detach();
        // Assert 2 module to the tenant
        $tenant->modules()->attach(Module::find($tenantModuleId));
        if ($tenantModuleId !== $userModuleId) $tenant->modules()->attach(Module::find($userModuleId));

        // Create a user to log in
        $user = User::factory()->create([
            'role' => $role,
            'tenant_id' => $tenant->id
        ]);

        // Acting as the user with given privilege
        auth()->login($user);
        $user->modules()->detach();
        // attach modules to tenant
        $user->modules()->attach(Module::find($userModuleId));

        // Attempt to create a new service
        return $this->post("/appointments/services/create-service", [
            'slug' => 'slug',
            'name' => 'Example Name',
            'opening_time' => '10:00:00',
            'closing_time' => '18:00:00',
            'status' => 'active',
        ]);
    }
}
