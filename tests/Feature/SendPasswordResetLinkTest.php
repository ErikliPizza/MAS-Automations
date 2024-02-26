<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendPasswordResetLinkTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    // Yes I'M crazy IVAN, any question?
    protected function createUserAndTenant($userStatus, $tenantStatus, $role, $expiryTime)
    {
        $tenant = Tenant::factory()->create([
            'status' => $tenantStatus,
            'expiry_time' => $expiryTime
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => $userStatus,
            'role' => $role
        ]);

        return $user;
    }

    public function testActiveUserActiveTenantAdminRoleNotExpired()
    {
        $user = $this->createUserAndTenant('active', 'active', 'admin', '2024-02-10 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('status');
    }
    public function testActiveUserActiveTenantRootRoleNotExpired()
    {
        $user = $this->createUserAndTenant('active', 'active', 'root', '2024-02-10 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('status');
    }

    public function testActiveUserActiveTenantAdminRoleExpired()
    {
        $user = $this->createUserAndTenant('active', 'active', 'admin', '2024-01-30 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testActiveUserActiveTenantNonAdminRoleNotExpired()
    {
        $user = $this->createUserAndTenant('active', 'active', 'additional', '2024-02-10 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testActiveUserActiveTenantNonAdminRoleExpired()
    {
        $user = $this->createUserAndTenant('active', 'active', 'additional', '2024-01-30 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testActiveUserInactiveTenantAnyRoleNotExpired()
    {
        $user = $this->createUserAndTenant('active', 'inactive', 'admin', '2024-02-10 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testActiveUserInactiveTenantAnyRoleExpired()
    {
        $user = $this->createUserAndTenant('active', 'inactive', 'admin', '2024-01-30 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testInactiveUserActiveTenantAnyRoleNotExpired()
    {
        $user = $this->createUserAndTenant('inactive', 'active', 'admin', '2024-02-10 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testInactiveUserActiveTenantAnyRoleExpired()
    {
        $user = $this->createUserAndTenant('inactive', 'active', 'admin', '2024-01-30 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testInactiveUserInactiveTenantAnyRoleNotExpired()
    {
        $user = $this->createUserAndTenant('inactive', 'inactive', 'admin', '2024-02-10 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }

    public function testInactiveUserInactiveTenantAnyRoleExpired()
    {
        $user = $this->createUserAndTenant('inactive', 'inactive', 'admin', '2024-01-30 00:00:00');
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHas('error');
    }
}
