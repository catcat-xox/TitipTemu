<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_user_dashboard(): void
    {
        $this->get('/akun')
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_hidden_login_for_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect(route('admin.login'));
    }

    public function test_user_can_access_only_user_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/akun')->assertOk();
        $this->actingAs($user)->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_only_admin_dashboard(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin, 'admin')->get('/akun')->assertForbidden();
    }
}
