<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $tenant1 = Tenant::factory()->create([
            'expiry_time' => '2024-07-01 00:00:00',
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'status' => 'active'
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $tenant1 = Tenant::factory()->create([
            'expiry_time' => '2024-07-01 00:00:00',
        ]);
        $user = User::factory()->create([
            'tenant_id' => $tenant1->id
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $tenant1 = Tenant::factory()->create([
            'expiry_time' => '2024-07-01 00:00:00',
        ]);
        $user = User::factory()->create([
            'tenant_id' => $tenant1->id
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
