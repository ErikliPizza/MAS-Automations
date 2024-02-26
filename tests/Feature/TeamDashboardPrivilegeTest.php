<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TeamDashboardPrivilegeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    // edit user //
    /** @test
     * User with Root privilege able to edit the admin and additional users on the team manager dashboard
     * But not able to edit root users on the team manager dashboard, -ironically there can only be one root user per organization/tenant
     *
     *   lists:
     *   2.3.1.a Only Root and Panel Can Edit a User
     * */
    public function root_can_edit_users_with_role_admin_and_additional_and_can_not_edit_root()
    {
        // root edits admin
        $responseForAdmin = $this->performUserEditTest('root', 'admin');
        $responseForAdmin->assertSessionHas('success');
        // root edits additional
        $responseForAdditional = $this->performUserEditTest('root', 'additional');
        $responseForAdditional->assertSessionHas('success');
        // root can not edit root
        $responseForRoot = $this->performUserEditTest('root', 'root');
        $responseForRoot->assertStatus(403);
    }

    /** @test
     * User with Panel privilege able to edit the additional users on the team manager dashboard
     * But not able to edit other admin or root users on the team manager dashboard
     *
     *  lists:
     *  1.1 Only Root Can Edit Admins
     *  1.2 Admins Can Edit Only Additional Users
     *  2.3.1.a Only Root and Panel Can Edit a User
     *
     *    */
    public function admin_can_edit_users_with_role_additional_and_can_not_edit_with_rol_admin_or_root()
    {
        // admin edits additional
        $responseForAdditional = $this->performUserEditTest('admin', 'additional');
        $responseForAdditional->assertSessionHas('success');

        // admin can not edit admin
        $responseForAdmin = $this->performUserEditTest('admin', 'admin');
        $responseForAdmin->assertStatus(403);

        // admin can not edit root
        $responseForRoot = $this->performUserEditTest('admin', 'root');
        $responseForRoot->assertStatus(403);
    }

    /** @test
     * Normal users are not able to edit any type of user on team manager dashboard
     * @IMPORTANT
     * The redirect is hard coded; may change in the future via canAccess middleware, remember to change it from here.
     *
     *   lists:
     *   1.3 Additional Users Can Not Edit Any User
     * */
    public function additional_users_can_not_edit_any_type_of_user()
    {
        // normal user can not edit additional
        $responseForAdditional = $this->performUserEditTest('additional', 'additional');
        $responseForAdditional->assertRedirect('/login');

        // normal user can not edit admin
        $responseForAdmin = $this->performUserEditTest('additional', 'admin');
        $responseForAdmin->assertRedirect('/login');

        // normal user can not edit root
        $responseForRoot = $this->performUserEditTest('additional', 'root');
        $responseForRoot->assertRedirect('/login');
    }

    /**
     *   lists:
     *   2.3.1.b User Can Not be Able To Edit Other Tenant's Users
     * */
    private function performUserEditTest($actingUserRole, $targetUserRole)
    {
        // create example modules
        $modules = Module::factory(5)->create();

        // create tenant
        $tenant1 = Tenant::factory()->create();

        // attach modules to tenant
        $tenant1->modules()->attach($modules);

        // Create a user to log in
        $actingUser = User::factory()->create([
            'role' => $actingUserRole,
            'tenant_id' => $tenant1->id
        ]);

        // mapping the user's tenant's module's id's to $modules variable to use it for updating the target user
        $modules = $actingUser->tenant->modules->pluck('id')->toArray();

        // Acting as the user with given privilege
        auth()->login($actingUser);

        // Create a target user, you can change the tenant id as you wish, it will be forced to use auth user's tenant id anyway ;)
        $targetUser = User::factory()->create([
            'role' => $targetUserRole,
            'tenant_id' => 34
        ]);

        // Attempt to edit the admin user
        return $this->put("/users/{$targetUser->id}", [
            'name' => 'New Name',
            'email' => $this->faker->email,
            'role' => 'additional',
            'status' => 'active',
            'modules' => $modules
            // Add other fields you want to test
        ]);

    }


    // create user
    /** @test
     * ROOT user can not be created via team manager panel by any user
     * Panel can create additional and admin users
     * Root can create additional and admin users
     * Additional can not create any user
     *  lists:
     *  1.4 No One Can Not Create a Root User
     *  2.1.a Only Root and Panel Can Create a User
     *
     * */
    public function create_users()
    {
        // admin creates additional user
        $response = $this->performUserCreateTest('admin', 'additional');
        $response->assertSessionHas('success');

        // admin creates admin user
        $response = $this->performUserCreateTest('admin', 'admin');
        $response->assertSessionHas('success');

        // admin can not create root user
        $response = $this->performUserCreateTest('admin', 'root');
        $response->assertInvalid();

        // root creates additional user
        $response = $this->performUserCreateTest('root', 'additional');
        $response->assertSessionHas('success');

        // root creates admin user
        $response = $this->performUserCreateTest('root', 'admin');
        $response->assertSessionHas('success');

        // root can not create root user
        $response = $this->performUserCreateTest('root', 'root');
        $response->assertSessionHas('error');

        // additional can not create additional user
        $response = $this->performUserCreateTest('additional', 'additional');
        $response->assertRedirect('/login');

        // additional can not create admin user
        $response = $this->performUserCreateTest('additional', 'admin');
        $response->assertRedirect('/login');

        // additional can not create root user
        $response = $this->performUserCreateTest('additional', 'root');
        $response->assertRedirect('/login');
    }

    private function performUserCreateTest($actingUserRole, $targetUserRole)
    {
        // create example modules
        $modules = Module::factory(5)->create();

        // create tenant
        $tenant1 = Tenant::factory()->create();

        // attach modules to tenant
        $tenant1->modules()->attach($modules);

        // Create a user to log in
        $actingUser = User::factory()->create([
            'role' => $actingUserRole,
            'tenant_id' => $tenant1->id
        ]);

        // mapping the user's tenant's module's id's to $modules variable to use it for updating the target user
        $modules = $actingUser->tenant->modules->pluck('id')->toArray();

        // Acting as the user with given privilege
        auth()->login($actingUser);


        // Attempt to edit the admin user
        return $this->post("/create-user", [
            'name' => 'New Name',
            'email' => $this->faker->email,
            'phone' => '12345678910',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => $targetUserRole,
            'status' => 'active',
            'modules' => $modules
            // Add other fields you want to test
        ]);
    }

    // delete user //
    /** @test
     * User with Root privilege able to delete the admin and additional users on the team manager dashboard
     * But not able to delete root users on the team manager dashboard
     * */
    public function root_can_delete_users_with_role_admin_and_additional_and_can_not_edit_root()
    {
        // root edits admin
        $responseForAdmin = $this->performUserDeleteTest('root', 'admin');
        $responseForAdmin->assertSessionHas('success');
        // root edits additional
        $responseForAdditional = $this->performUserDeleteTest('root', 'additional');
        $responseForAdditional->assertSessionHas('success');
        // root can not edit root
        $responseForRoot = $this->performUserDeleteTest('root', 'root');
        $responseForRoot->assertStatus(403);
    }

    /** @test
     * User with Admin privilege able to edit the additional users on the team manager dashboard
     * But not able to delete other admin or root users on the team manager dashboard
     *
     *    */
    public function admin_can_delete_users_with_role_additional_and_can_not_edit_with_rol_admin_or_root()
    {
        // admin edits additional
        $responseForAdditional = $this->performUserDeleteTest('admin', 'additional');
        $responseForAdditional->assertSessionHas('success');

        // admin can not edit admin
        $responseForAdmin = $this->performUserDeleteTest('admin', 'admin');
        $responseForAdmin->assertStatus(403);

        // admin can not edit root
        $responseForRoot = $this->performUserDeleteTest('admin', 'root');
        $responseForRoot->assertStatus(403);
    }

    /** @test
     * Normal users are not able to delete any type of user on team manager dashboard
     * @IMPORTANT
     * The redirect is hard coded; may change in the future via canAccess middleware, remember to change it from here.
     * */
    public function additional_users_can_not_delete_any_type_of_user()
    {
        // normal user can not edit additional
        $responseForAdditional = $this->performUserDeleteTest('additional', 'additional');
        $responseForAdditional->assertRedirect('/login');

        // normal user can not edit admin
        $responseForAdmin = $this->performUserDeleteTest('additional', 'admin');
        $responseForAdmin->assertRedirect('/login');

        // normal user can not edit root
        $responseForRoot = $this->performUserDeleteTest('additional', 'root');
        $responseForRoot->assertRedirect('/login');
    }

    /** @Test
     * Deletable test
     * */
    private function performUserDeleteTest($actingUserRole, $targetUserRole)
    {
        // create tenant
        $tenant1 = Tenant::factory()->create();

        // Create a user to log in
        $actingUser = User::factory()->create([
            'role' => $actingUserRole,
            'tenant_id' => $tenant1->id
        ]);

        // mapping the user's tenant's module's id's to $modules variable to use it for updating the target user
        $modules = $actingUser->tenant->modules->pluck('id')->toArray();

        // Acting as the user with given privilege
        auth()->login($actingUser);

        // Create a target user, you can change the tenant id as you wish, it will be forced to use auth user's tenant id anyway ;)
        $targetUser = User::factory()->create([
            'role' => $targetUserRole,
            'tenant_id' => 34
        ]);

        // Attempt to edit the admin user
        return $this->delete("/users/{$targetUser->id}");
    }


}
