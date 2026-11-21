<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModerationQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_queue(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_queue(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_sees_pending_reports_by_default_without_private_owner_data(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create([
            'name' => 'Pemilik Test',
            'email' => 'pemilik-rahasia@titiptemu.test',
            'nim_nip' => 'NIM-RAHASIA',
        ]);

        LostReport::factory()->for($owner)->create(['item_name' => 'Kunci Pending']);
        LostReport::factory()->for($owner)->approved()->create(['item_name' => 'Kunci Disetujui']);
        LostReport::factory()->for($owner)->rejected()->create(['item_name' => 'Kunci Ditolak']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Kunci Pending')
            ->assertSee('Pemilik Test')
            ->assertDontSee('Kunci Disetujui')
            ->assertDontSee('Kunci Ditolak')
            ->assertDontSee('pemilik-rahasia@titiptemu.test')
            ->assertDontSee('NIM-RAHASIA');
    }

    public function test_admin_can_filter_queue_by_status(): void
    {
        $admin = User::factory()->admin()->create();

        LostReport::factory()->create(['item_name' => 'Kunci Pending']);
        LostReport::factory()->approved()->create(['item_name' => 'Kunci Disetujui']);

        $this->actingAs($admin)->get(route('admin.dashboard', ['status' => 'approved']))
            ->assertOk()
            ->assertSee('Kunci Disetujui')
            ->assertDontSee('Kunci Pending');
    }

    public function test_invalid_status_falls_back_to_pending_queue(): void
    {
        $admin = User::factory()->admin()->create();

        LostReport::factory()->create(['item_name' => 'Kunci Pending']);

        $this->actingAs($admin)->get(route('admin.dashboard', ['status' => 'tidak-valid']))
            ->assertOk()
            ->assertSee('Kunci Pending');
    }
}
