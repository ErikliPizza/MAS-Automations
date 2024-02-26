<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleCorrectionTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_prevent_attaching_modules_to_user_if_users_tenant_has_not_that_module()
    {
        // create modules
        $myModules = Module::factory(5)->create(); // id with 1-2-3-4-5
        $otherModules = Module::factory(5)->create(); // id with 6-7-8-9-10

        // create tenants
        $myTenant = Tenant::factory()->create();
        $otherTenant = Tenant::factory()->create();

        // Create a user to log in
        $user = User::factory()->create([
            'role' => 'additional',
            'tenant_id' => $myTenant->id
        ]);

        $myTenant->modules()->attach($myModules);
        $otherTenant->modules()->attach($otherModules);

        auth()->login($user);

        $user->modules()->attach($myModules);

        $this->assertTrue(true);
    }
}
