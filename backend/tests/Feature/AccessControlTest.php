<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_public_and_auth_pages(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_guest_cannot_access_user_or_admin_dashboard(): void
    {
        $this->get('/akun')->assertRedirect(route('login'));
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
        $this->post('/logout')->assertRedirect(route('login'));
    }

    public function test_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/akun')->assertOk();
        $this->actingAs($user)->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_admin_cannot_access_user_dashboard(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin, 'admin')->get('/akun')->assertForbidden();
    }
}
