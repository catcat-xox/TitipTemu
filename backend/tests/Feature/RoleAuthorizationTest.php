<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_user_dashboard(): void
    {
        $this->get('/user/dashboard')
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_for_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect(route('login'));
    }

    public function test_user_can_access_only_user_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/user/dashboard')->assertOk();
        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admin_can_access_only_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get('/user/dashboard')->assertForbidden();
    }
}
