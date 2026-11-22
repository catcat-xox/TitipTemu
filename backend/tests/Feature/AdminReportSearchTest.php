<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_hidden_admin_login_from_report_search(): void
    {
        $this->get(route('admin.reports.search'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_access_report_search(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.reports.search'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_search_page_lists_reports_from_every_status(): void
    {
        $admin = Admin::factory()->create();
        $statuses = ['pending', 'approved', 'return_pending', 'rejected', 'returned', 'archived', 'cancelled'];

        foreach ($statuses as $status) {
            LostReport::factory()->create([
                'status' => $status,
                'item_name' => 'Laporan '.ucfirst($status),
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.reports.search'))
            ->assertOk()
            ->assertSee('Cari laporan')
            ->assertSee('Menampilkan semua laporan.');

        foreach ($statuses as $status) {
            $response->assertSee('Laporan '.ucfirst($status));
        }
    }

    public function test_admin_can_search_reports_by_public_and_private_fields(): void
    {
        $admin = Admin::factory()->create();
        $owner = User::factory()->create(['name' => 'Pelapor Istimewa']);
        $match = LostReport::factory()->for($owner)->create([
            'item_name' => 'Dompet Unik',
            'category' => 'Dokumen',
            'location' => 'Perpustakaan',
            'description' => 'KataKhusus pada deskripsi.',
            'contact_phone' => '08129998888',
            'status' => 'approved',
        ]);
        LostReport::factory()->create([
            'item_name' => 'Kunci Biasa',
            'status' => 'pending',
        ]);

        foreach (['Unik', 'Dokumen', 'Perpustakaan', 'KataKhusus', '08129998888', 'Istimewa'] as $keyword) {
            $this->actingAs($admin, 'admin')->get(route('admin.reports.search', ['q' => $keyword]))
                ->assertOk()
                ->assertSee($match->item_name)
                ->assertDontSee('Kunci Biasa');
        }
    }

    public function test_admin_search_shows_empty_state_for_unmatched_keyword(): void
    {
        $admin = Admin::factory()->create();
        LostReport::factory()->create(['item_name' => 'Dompet Kulit']);

        $this->actingAs($admin, 'admin')->get(route('admin.reports.search', ['q' => 'TidakAda']))
            ->assertOk()
            ->assertSee('Belum ada laporan yang sesuai')
            ->assertDontSee('Dompet Kulit');
    }
}
