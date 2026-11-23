<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LayoutSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_and_user_pages_use_public_layout_without_admin_shell(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('site-header', false)
            ->assertSee('site-footer', false)
            ->assertDontSee('admin-shell', false)
            ->assertDontSee('admin-sidebar', false)
            ->assertDontSee('Keluar Admin');

        $user = User::factory()->create();

        $this->actingAs($user)->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('site-header', false)
            ->assertSee('site-footer', false)
            ->assertDontSee('admin-shell', false)
            ->assertDontSee('admin-sidebar', false)
            ->assertDontSee('Keluar Admin');
    }

    public function test_hidden_admin_login_uses_admin_auth_layout_without_public_navigation(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('admin-auth-main', false)
            ->assertSee('admin-auth-card', false)
            ->assertDontSee('class="auth-section"', false)
            ->assertDontSee('site-header', false)
            ->assertDontSee('site-footer', false)
            ->assertDontSee('Navigasi utama', false);
    }

    public function test_admin_pages_use_admin_layout_without_public_navigation_or_footer(): void
    {
        $admin = Admin::factory()->create();
        $report = LostReport::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('admin-shell', false)
            ->assertSee('admin-sidebar', false)
            ->assertDontSee('site-header', false)
            ->assertDontSee('site-footer', false)
            ->assertDontSee('Navigasi utama', false)
            ->assertDontSee('Daftar', false);

        $this->actingAs($admin, 'admin')->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('admin-shell', false)
            ->assertDontSee('site-header', false)
            ->assertDontSee('site-footer', false);

        $this->actingAs($admin, 'admin')->get(route('admin.reports.edit', $report))
            ->assertOk()
            ->assertSee('admin-shell', false)
            ->assertDontSee('site-header', false)
            ->assertDontSee('site-footer', false);
    }

    public function test_admin_is_redirected_to_admin_dashboard_from_public_and_auth_pages(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('home'))
            ->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin, 'admin')->get(route('guide'))
            ->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin, 'admin')->get(route('reports.index', ['hilang']))
            ->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin, 'admin')->get(route('login'))
            ->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin, 'admin')->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_cannot_use_user_routes(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('user.dashboard'))
            ->assertForbidden();
        $this->actingAs($admin, 'admin')->get(route('reports.create'))
            ->assertForbidden();
    }

    public function test_guest_and_user_cannot_use_admin_routes(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));

        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_redirects_directly_to_admin_dashboard(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'layout.admin@titiptemu.test',
            'password' => Hash::make('rahasia-admin'),
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'layout.admin@titiptemu.test',
            'password' => 'rahasia-admin',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('admin-shell', false)
            ->assertDontSee('site-header', false);
    }
}
