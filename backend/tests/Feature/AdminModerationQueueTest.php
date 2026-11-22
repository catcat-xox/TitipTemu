<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModerationQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_hidden_admin_login_from_admin_queue(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_access_admin_queue(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_sees_pending_reports_by_default_without_private_owner_data(): void
    {
        $admin = Admin::factory()->create();
        $owner = User::factory()->create([
            'name' => 'Pemilik Test',
            'email' => 'pemilik-rahasia@titiptemu.test',
            'nim_nip' => 'NIM-RAHASIA',
        ]);

        LostReport::factory()->for($owner)->create(['item_name' => 'Kunci Pending']);
        LostReport::factory()->for($owner)->approved()->create(['item_name' => 'Kunci Disetujui']);
        LostReport::factory()->for($owner)->rejected()->create(['item_name' => 'Kunci Ditolak']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Kunci Pending')
            ->assertSee('Pemilik Test')
            ->assertSee('MENUNGGU')
            ->assertSee('PENGEMBALIAN')
            ->assertSee('Menunggu pemeriksaan')
            ->assertSee('Pengembalian diperiksa')
            ->assertSee('Kembali ke halaman sebelumnya')
            ->assertDontSee('Tinjau antrean laporan barang temuan yang masuk untuk diverifikasi')
            ->assertDontSee('admin-topbar', false)
            ->assertDontSee('Panel Moderasi')
            ->assertDontSee('Kunci Disetujui')
            ->assertDontSee('Kunci Ditolak')
            ->assertDontSee('pemilik-rahasia@titiptemu.test')
            ->assertDontSee('NIM-RAHASIA');
    }

    public function test_admin_can_filter_queue_by_status(): void
    {
        $admin = Admin::factory()->create();

        LostReport::factory()->create(['item_name' => 'Kunci Pending']);
        LostReport::factory()->approved()->create(['item_name' => 'Kunci Disetujui']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard', ['status' => 'approved']))
            ->assertOk()
            ->assertSee('Kunci Disetujui')
            ->assertDontSee('Kunci Pending');
    }

    public function test_admin_can_view_all_reports_with_all_status_filter(): void
    {
        $admin = Admin::factory()->create();

        LostReport::factory()->create(['item_name' => 'Kunci Pending']);
        LostReport::factory()->approved()->create(['item_name' => 'Kunci Disetujui']);
        LostReport::factory()->rejected()->create(['item_name' => 'Kunci Ditolak']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard', ['status' => 'all']))
            ->assertOk()
            ->assertSee('Semua (3)')
            ->assertSee('Kunci Pending')
            ->assertSee('Kunci Disetujui')
            ->assertSee('Kunci Ditolak');
    }

    public function test_admin_pagination_uses_bootstrap_five_markup(): void
    {
        $admin = Admin::factory()->create();

        foreach (range(1, 11) as $number) {
            LostReport::factory()->create(['item_name' => 'Laporan '.$number]);
        }

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('class="pagination"', false)
            ->assertSee('class="page-item', false)
            ->assertSee('class="page-link"', false)
            ->assertSee('rel="next"', false)
            ->assertDontSee('inline-flex items-center px-2 py-2', false);
    }

    public function test_report_review_header_uses_back_button_without_menu_location(): void
    {
        $admin = Admin::factory()->create();
        $report = LostReport::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('Tinjau laporan')
            ->assertSee('Kembali ke halaman sebelumnya')
            ->assertDontSee('Kembali ke antrean')
            ->assertDontSee('Area Admin');
    }

    public function test_invalid_status_falls_back_to_pending_queue(): void
    {
        $admin = Admin::factory()->create();

        LostReport::factory()->create(['item_name' => 'Kunci Pending']);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard', ['status' => 'tidak-valid']))
            ->assertOk()
            ->assertSee('Kunci Pending');
    }
}
