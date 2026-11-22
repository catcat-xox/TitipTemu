<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDualReportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_has_five_entries_with_logout_at_the_bottom(): void
    {
        $admin = Admin::factory()->create(['name' => 'Admin Menu']);
        $html = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Halo, Admin Menu', $html);
        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('Cari laporan', $html);
        $this->assertStringContainsString('Barang Hilang', $html);
        $this->assertStringContainsString('Barang Ditemukan', $html);
        $this->assertStringContainsString('>Keluar Admin</button>', $html);

        $navEnd = strpos($html, '</nav>');
        $logout = strpos($html, 'Keluar Admin');
        $this->assertNotFalse($navEnd);
        $this->assertNotFalse($logout);
        $this->assertGreaterThan($navEnd, $logout);
    }

    public function test_dashboard_filters_each_report_kind_and_status_cards_stay_in_the_active_kind(): void
    {
        $admin = Admin::factory()->create();
        MissingReport::factory()->create(['item_name' => 'Kunci Hilang']);
        LostReport::factory()->create(['item_name' => 'Kunci Ditemukan']);

        $lostHtml = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard', ['jenis' => 'lost', 'status' => 'all']))
            ->assertOk()
            ->getContent();
        $this->assertStringContainsString('Kunci Hilang', $lostHtml);
        $this->assertStringNotContainsString('Kunci Ditemukan', $lostHtml);
        $this->assertStringContainsString('table-admin-compact', $lostHtml);
        $this->assertStringContainsString('d-flex flex-nowrap gap-2 justify-content-end', $lostHtml);
        $this->assertStringContainsString('justify-content-lg-end', $lostHtml);
        $this->assertLessThan(
            strpos($lostHtml, '>Terapkan</button>'),
            strpos($lostHtml, 'Status laporan'),
            'Tombol Terapkan harus berada setelah dropdown status.'
        );
        foreach (['pending', 'approved', 'verification', 'done'] as $filter) {
            $this->assertStringContainsString(
                e(route('admin.dashboard', ['jenis' => 'lost', 'status' => $filter])),
                $lostHtml
            );
        }

        $foundHtml = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard', ['jenis' => 'found', 'status' => 'all']))
            ->assertOk()
            ->getContent();
        $this->assertStringContainsString('Kunci Ditemukan', $foundHtml);
        $this->assertStringNotContainsString('Kunci Hilang', $foundHtml);
        foreach (['pending', 'approved', 'verification', 'done'] as $filter) {
            $this->assertStringContainsString(
                e(route('admin.dashboard', ['jenis' => 'found', 'status' => $filter])),
                $foundHtml
            );
        }
    }

    public function test_admin_search_covers_both_report_kinds(): void
    {
        $admin = Admin::factory()->create();
        $missing = MissingReport::factory()->create(['item_name' => 'Dompet Pemilik']);
        $lost = LostReport::factory()->create(['item_name' => 'Dompet Penemu']);

        $html = $this->actingAs($admin, 'admin')->get(route('admin.reports.search'))->assertOk()->getContent();

        $this->assertStringContainsString('Dompet Pemilik', $html);
        $this->assertStringContainsString('Dompet Penemu', $html);
        $this->assertStringContainsString('Barang Hilang', $html);
        $this->assertStringContainsString('Barang Ditemukan', $html);
        $this->assertStringContainsString(route('admin.reports.missing.show', $missing), $html);
        $this->assertStringContainsString(route('admin.reports.show', $lost), $html);
    }

    public function test_admin_can_review_and_approve_a_missing_report(): void
    {
        $admin = Admin::factory()->create();
        $owner = User::factory()->create(['name' => 'Pemilik Laporan']);
        $report = MissingReport::factory()->for($owner)->create(['item_name' => 'Tas Kulit']);

        $this->actingAs($admin, 'admin')->get(route('admin.reports.missing.show', $report))
            ->assertOk()
            ->assertSee('Tinjau laporan kehilangan')
            ->assertSee('Tas Kulit')
            ->assertSee('No. HP pemilik')
            ->assertSee('Setujui dan tampilkan di Barang Hilang');

        $this->post(route('admin.reports.missing.verify', $report), ['decision' => 'approve'])
            ->assertRedirect(route('admin.dashboard', ['jenis' => 'lost', 'status' => 'approved']));

        $this->assertSame(MissingReport::STATUS_APPROVED, $report->fresh()->status);
        $this->assertDatabaseHas('missing_report_status_histories', [
            'missing_report_id' => $report->id,
            'admin_id' => $admin->id,
            'action' => 'approve',
        ]);
    }
}
