<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginAttemptTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @Test
     * list:
     * 1- User status is "inactive", user can not log in
     */
    public function test_user_can_not_login_if_the_status_is_not_active()
    {
        $tenant1 = Tenant::factory()->create();

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
            'status' => 'inactive'
        ]);
        $this->post('/login', [
            'email' => $user1->email,
            'password' => 'password',
        ]);
        $this->assertGuest();
    }

    /** @Test
     * list:
     * 2- Tenant status is "inactive", user can not log in
     */
    public function test_user_can_not_login_if_the_tenant_status_is_not_active()
    {
        $tenant1 = Tenant::factory()->create([
            'status' => 'inactive'
        ]);

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
            'status' => 'active'
        ]);
        $this->post('/login', [
            'email' => $user1->email,
            'password' => 'password',
        ]);
        $this->assertGuest();
    }

    /** @Test
     * list:
     * 3- Tenant expiry_date is expired, user can not log in
     */
    public function test_user_can_not_login_if_the_tenant_expiry_date_is_expired()
    {
        $tenant1 = Tenant::factory()->create([
            'status' => 'active',
            'expiry_time' => '2000-01-31 15:56:45'
        ]);

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
            'status' => 'active'
        ]);
        $this->post('/login', [
            'email' => $user1->email,
            'password' => 'password',
        ]);
        $this->assertGuest();
    }

    /** @Test
     * list:
     * 4- Force to log out the user if it is inactive while logged in
     */
    public function test_user_log_outs_if_is_inactive_while_surfing_in_restricted_routes()
    {
        $tenant1 = Tenant::factory()->create([
            'status' => 'active',
            'expiry_time' => '2025-01-31 15:56:45'
        ]);

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
            'status' => 'active',
            'role' => 'admin'
        ]);
        $this->post('/login', [
            'email' => $user1->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        Auth::user()->status = 'inactive';
        Auth::user()->update();

        $this->get('/create-user'); // example only-auth-accessable area

        $this->assertGuest();
    }

    /** @Test
     * list:
     * 5- Force to log out the user if related tenant is inactive while the user logged in
     */
    public function test_user_log_outs_if_tenant_is_inactive_while_surfing_in_restricted_routes()
    {
        $tenant1 = Tenant::factory()->create([
            'status' => 'active',
            'expiry_time' => '2025-01-31 15:56:45'
        ]);

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
            'status' => 'active',
            'role' => 'admin'
        ]);
        $this->post('/login', [
            'email' => $user1->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        Auth::user()->tenant->status = 'inactive';
        Auth::user()->tenant->update();

        $this->get('/create-user'); // example only-auth-accessable area

        $this->assertGuest();
    }

    /** @Test
     * list:
     * 6- Force to log out the user if related tenant has expired while the user logged in
     */
    public function test_user_log_outs_if_tenant_subscription_is_expired_while_surfing_in_restricted_routes()
    {
        $tenant1 = Tenant::factory()->create([
            'status' => 'active',
            'expiry_time' => '2025-01-31 15:56:45'
        ]);

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
            'status' => 'active',
            'role' => 'admin'
        ]);
        $this->post('/login', [
            'email' => $user1->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        Auth::user()->tenant->expiry_time = '2000-01-31 15:56:45';
        Auth::user()->tenant->update();

        $this->get('/create-user'); // example only-auth-accessable area

        $this->assertGuest();
    }
}
