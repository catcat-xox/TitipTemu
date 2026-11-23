<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hidden_admin_login_page_is_accessible_but_not_linked_from_public_pages(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Masuk ke panel Admin');

        $this->get('/')
            ->assertOk()
            ->assertDontSee((string) config('admin.login_uri'))
            ->assertDontSee('Dashboard Admin')
            ->assertDontSee('Masuk Admin');

        $this->get(route('guide'))
            ->assertOk()
            ->assertDontSee((string) config('admin.login_uri'));
    }

    public function test_admin_can_login_and_logout_through_hidden_form(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin.login@titiptemu.test',
            'password' => Hash::make('rahasia-admin'),
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'admin.login@titiptemu.test',
            'password' => 'rahasia-admin',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->get(route('admin.dashboard'))->assertOk();

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_invalid_admin_credentials_are_rejected(): void
    {
        Admin::factory()->create([
            'email' => 'admin@titiptemu.test',
            'password' => Hash::make('rahasia-admin'),
        ]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'email' => 'admin@titiptemu.test',
                'password' => 'salah',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_user_account_cannot_login_through_admin_form(): void
    {
        $user = User::factory()->create([
            'email' => 'user.biasa@titiptemu.test',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_legacy_admin_user_row_cannot_login_through_user_form(): void
    {
        $legacyAdmin = User::factory()->admin()->create([
            'email' => 'legacy.admin@titiptemu.test',
        ]);

        $this->post(route('login.store'), [
            'email' => $legacyAdmin->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
