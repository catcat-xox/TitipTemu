<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MissingReportManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_guest_and_admin_cannot_access_missing_report_routes(): void
    {
        $report = MissingReport::factory()->create();

        $this->get(route('missing-reports.create'))->assertRedirect(route('login'));
        $this->post(route('missing-reports.store'))->assertRedirect(route('login'));
        $this->get(route('missing-reports.mine'))->assertRedirect(route('login'));
        $this->get(route('missing-reports.edit', $report))->assertRedirect(route('login'));
        $this->put(route('missing-reports.update', $report), [])->assertRedirect(route('login'));
        $this->post(route('missing-reports.cancel', $report))->assertRedirect(route('login'));
        $this->post(route('missing-reports.found', $report))->assertRedirect(route('login'));
        $this->post(route('missing-reports.resubmit', $report))->assertRedirect(route('login'));

        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin')->get(route('missing-reports.create'))->assertForbidden();
        $this->actingAs($admin, 'admin')->get(route('missing-reports.mine'))->assertForbidden();
    }

    public function test_owner_can_submit_missing_report_with_required_private_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('missing-reports.store'), $this->payload())
            ->assertRedirect(route('missing-reports.mine'));

        $report = MissingReport::firstOrFail();
        $this->assertDatabaseHas('missing_reports', [
            'id' => $report->id,
            'user_id' => $user->id,
            'item_name' => 'Dompet hilang',
            'status' => MissingReport::STATUS_PENDING,
            'contact_phone' => '081234567890',
        ]);
        $this->assertNotNull($report->terms_accepted_at);
        $this->assertStringStartsWith('missing-reports/', $report->photo_path);
        Storage::disk('local')->assertExists($report->photo_path);
        $this->assertSame(0, LostReport::count());

        foreach (['/', route('reports.index', 'hilang'), route('reports.index', 'ditemukan')] as $uri) {
            $this->get($uri)->assertOk()->assertDontSee('081234567890');
        }
    }

    public function test_missing_report_form_requires_owner_phone_photo_and_terms(): void
    {
        $user = User::factory()->create();
        $html = $this->actingAs($user)->get(route('missing-reports.create'))->assertOk()->getContent();

        $this->assertStringContainsString('No. HP pemilik *', $html);
        $this->assertStringContainsString('Tanggal hilang *', $html);
        $this->assertStringContainsString('Lokasi hilang *', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="contact_phone"[^>]*required/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="photo"[^>]*required/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="terms_accepted"[^>]*required/', $html);

        $withoutFiles = $this->payload();
        unset($withoutFiles['photo'], $withoutFiles['terms_accepted']);
        $this->actingAs($user)->post(route('missing-reports.store'), $withoutFiles)
            ->assertSessionHasErrors(['photo', 'terms_accepted']);

        $this->actingAs($user)->post(route('missing-reports.store'), $this->payload([
            'contact_phone' => 'bukan nomor',
        ]))->assertSessionHasErrors('contact_phone');

        $this->assertSame(0, MissingReport::count());
    }

    public function test_account_shows_lists_for_both_report_types_without_create_buttons(): void
    {
        $user = User::factory()->create(['phone' => '0812-1111-1111']);
        $missing = MissingReport::factory()->for($user)->create([
            'item_name' => 'Kunci hilang',
            'contact_phone' => '0812-9999-9999',
        ]);
        LostReport::factory()->for($user)->create(['item_name' => 'Dompet ditemukan']);
        MissingReport::factory()->create(['item_name' => 'Laporan orang lain']);

        $html = $this->actingAs($user)->get(route('user.dashboard'))->assertOk()->getContent();

        $laporanSaya = strpos($html, 'Laporan saya');
        $laporanKehilangan = strpos($html, 'Laporan kehilangan');
        $this->assertNotFalse($laporanSaya);
        $this->assertNotFalse($laporanKehilangan);
        $reportSection = substr($html, $laporanSaya, $laporanKehilangan - $laporanSaya);

        $this->assertStringNotContainsString('<a class="btn btn-primary"', $reportSection);
        $this->assertStringNotContainsString('Laporkan Kehilangan Barang', $reportSection);
        $this->assertStringNotContainsString('Laporkan Barang Temuan', $reportSection);
        $this->assertStringContainsString('Pantau status laporan kehilangan dan laporan temuan milikmu.', $reportSection);
        $this->assertStringContainsString('Laporan kehilangan', $html);
        $this->assertStringContainsString('Laporan barang temuan', $html);
        $this->assertStringContainsString('Kunci hilang', $html);
        $this->assertStringContainsString('Dompet ditemukan', $html);
        $this->assertStringNotContainsString('Laporan orang lain', $html);
        $this->assertStringNotContainsString('0812-9999-9999', $html);
        $this->assertSame($user->id, $missing->user_id);
    }

    public function test_account_has_no_create_buttons_and_keeps_management_link(): void
    {
        $user = User::factory()->create();

        $html = $this->actingAs($user)->get(route('user.dashboard'))->assertOk()->getContent();

        $this->assertStringNotContainsString(
            '<a class="btn btn-primary" href="'.route('missing-reports.create').'">Laporkan Kehilangan Barang</a>',
            $html
        );
        $this->assertStringNotContainsString(
            '<a class="btn btn-primary" href="'.route('reports.create').'">Laporkan Barang Temuan</a>',
            $html
        );
        $this->assertStringContainsString('<a class="dropdown-item" href="'.route('missing-reports.create').'">', $html);
        $this->assertStringContainsString('<a class="dropdown-item" href="'.route('reports.create').'">', $html);
        $this->assertStringContainsString(route('missing-reports.mine'), $html);
        $this->assertStringContainsString('Kelola laporan kehilangan', $html);
        $this->assertLessThan(
            strpos($html, 'Laporan saya'),
            strpos($html, 'profile-card'),
            'Profil harus berada tepat sebelum Laporan saya.'
        );
    }

    public function test_missing_report_management_links_to_public_detail_when_visible(): void
    {
        $owner = User::factory()->create();
        $report = MissingReport::factory()->for($owner)->approved()->create([
            'item_name' => 'Kunci ditemukan pemiliknya',
        ]);

        $html = $this->actingAs($owner)->get(route('missing-reports.mine'))->assertOk()->getContent();
        $detailUrl = route('reports.show', ['hilang', $report]);

        $this->assertStringContainsString($detailUrl, $html);
        $this->assertStringContainsString(
            '<a class="btn btn-sm btn-outline-primary" href="'.$detailUrl.'">Detail</a>',
            $html
        );
        $this->assertStringNotContainsString('btn btn-sm btn-link', $html);
        $this->get($detailUrl)->assertOk();
    }

    public function test_only_owner_can_manage_missing_report_and_status_rules_apply(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $pending = MissingReport::factory()->for($owner)->create();
        $approved = MissingReport::factory()->for($owner)->approved()->create();

        $this->actingAs($stranger)->get(route('missing-reports.edit', $pending))->assertForbidden();
        $this->actingAs($stranger)->post(route('missing-reports.cancel', $pending))->assertForbidden();
        $this->actingAs($stranger)->post(route('missing-reports.found', $approved))->assertForbidden();
        $this->assertSame(MissingReport::STATUS_APPROVED, $approved->fresh()->status);

        $this->actingAs($owner)->get(route('missing-reports.edit', $pending))->assertOk();
        $this->actingAs($owner)->put(route('missing-reports.update', $pending), $this->payload([
            'item_name' => 'Kunci diperbarui',
        ]))->assertRedirect(route('missing-reports.mine'));
        $this->assertSame('Kunci diperbarui', $pending->fresh()->item_name);

        $this->actingAs($owner)->get(route('missing-reports.edit', $approved))->assertForbidden();
        $this->actingAs($owner)->post(route('missing-reports.cancel', $approved))
            ->assertRedirect(route('missing-reports.mine'))
            ->assertSessionHas('success');
        $this->assertSame(MissingReport::STATUS_CANCELLED, $approved->fresh()->status);
        $this->assertDatabaseHas('missing_report_status_histories', [
            'missing_report_id' => $approved->id,
            'actor_id' => $owner->id,
            'admin_id' => null,
            'action' => 'cancel_by_owner',
            'from_status' => MissingReport::STATUS_APPROVED,
            'to_status' => MissingReport::STATUS_CANCELLED,
        ]);

        $rejected = MissingReport::factory()->for($owner)->rejected()->create();
        $this->actingAs($owner)->post(route('missing-reports.resubmit', $rejected))
            ->assertRedirect(route('missing-reports.mine'));
        $this->assertSame(MissingReport::STATUS_PENDING, $rejected->fresh()->status);
    }

    public function test_owner_can_mark_approved_missing_report_found_and_start_thirty_day_window(): void
    {
        $owner = User::factory()->create();
        $report = MissingReport::factory()->for($owner)->approved()->create([
            'item_name' => 'Kunci ditemukan langsung',
        ]);

        $mineHtml = $this->actingAs($owner)->get(route('missing-reports.mine'))->assertOk()->getContent();
        $dashboardHtml = $this->actingAs($owner)->get(route('user.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString(route('missing-reports.found', $report), $mineHtml);
        $this->assertStringContainsString('Barang ditemukan', $mineHtml);
        $this->assertStringContainsString(route('missing-reports.found', $report), $dashboardHtml);
        $this->assertStringContainsString(route('missing-reports.cancel', $report), $dashboardHtml);

        $mineDetailPos = strpos($mineHtml, route('reports.show', ['hilang', $report]));
        $mineFoundPos = strpos($mineHtml, route('missing-reports.found', $report));
        $this->assertNotFalse($mineDetailPos);
        $this->assertGreaterThan($mineDetailPos, $mineFoundPos, 'Tombol Barang ditemukan harus berada paling kanan setelah Detail.');

        $dashboardAllPos = strrpos($dashboardHtml, '>Lihat semua</a>');
        $dashboardFoundPos = strpos($dashboardHtml, route('missing-reports.found', $report));
        $this->assertNotFalse($dashboardAllPos);
        $this->assertGreaterThan($dashboardAllPos, $dashboardFoundPos, 'Tombol Barang ditemukan harus berada paling kanan setelah Lihat semua.');

        $this->actingAs($owner)->post(route('missing-reports.found', $report))
            ->assertRedirect(route('missing-reports.mine'))
            ->assertSessionHas('success');

        $report->refresh();
        $this->assertSame(MissingReport::STATUS_FOUND, $report->status);
        $this->assertNotNull($report->found_at);
        $this->assertTrue($report->archiveDueAt()->equalTo($report->found_at->copy()->addDays(30)));
        $this->assertDatabaseHas('missing_report_status_histories', [
            'missing_report_id' => $report->id,
            'actor_id' => $owner->id,
            'admin_id' => null,
            'action' => 'found_by_owner',
            'from_status' => MissingReport::STATUS_APPROVED,
            'to_status' => MissingReport::STATUS_FOUND,
        ]);

        $this->get(route('reports.index', ['hilang', 'status' => 'sudah']))
            ->assertSee($report->item_name);
        $this->get(route('reports.show', ['hilang', $report]))->assertOk();
    }

    public function test_owner_found_action_is_only_available_for_approved_reports(): void
    {
        $owner = User::factory()->create();
        $approved = MissingReport::factory()->for($owner)->approved()->create(['item_name' => 'Approved unik']);
        $pending = MissingReport::factory()->for($owner)->create(['item_name' => 'Pending unik']);
        $found = MissingReport::factory()->for($owner)->foundAt(now()->subDay())->create(['item_name' => 'Found unik']);

        $mineHtml = $this->actingAs($owner)->get(route('missing-reports.mine'))->assertOk()->getContent();
        $dashboardHtml = $this->actingAs($owner)->get(route('user.dashboard'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($mineHtml, route('missing-reports.found', $approved)));
        $this->assertSame(0, substr_count($mineHtml, route('missing-reports.found', $pending)));
        $this->assertSame(0, substr_count($mineHtml, route('missing-reports.found', $found)));
        $this->assertSame(1, substr_count($dashboardHtml, route('missing-reports.found', $approved)));
        $this->assertSame(0, substr_count($dashboardHtml, route('missing-reports.found', $found)));
        $this->assertSame(1, substr_count($mineHtml, route('missing-reports.cancel', $approved)));
        $this->assertSame(1, substr_count($mineHtml, route('missing-reports.cancel', $pending)));
        $this->assertSame(0, substr_count($mineHtml, route('missing-reports.cancel', $found)));
    }

    public function test_owner_cannot_mark_non_approved_missing_report_as_found(): void
    {
        $owner = User::factory()->create();
        $reports = [
            MissingReport::factory()->for($owner)->create(),
            MissingReport::factory()->for($owner)->rejected()->create(),
            MissingReport::factory()->for($owner)->foundPending()->create(),
            MissingReport::factory()->for($owner)->foundAt(now())->create(),
            MissingReport::factory()->for($owner)->archived()->create(),
            MissingReport::factory()->for($owner)->create(['status' => MissingReport::STATUS_CANCELLED]),
        ];

        foreach ($reports as $report) {
            $originalStatus = $report->status;
            $this->actingAs($owner)->post(route('missing-reports.found', $report))->assertForbidden();
            $this->assertSame($originalStatus, $report->fresh()->status);
        }

        $this->assertSame(0, MissingReportStatusHistory::where('action', 'found_by_owner')->count());
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'item_name' => 'Dompet hilang',
            'category' => 'Aksesori',
            'description' => 'Dompet berwarna biru tertinggal di area kampus.',
            'lost_at' => now()->toDateString(),
            'location' => 'Perpustakaan',
            'contact_phone' => '081234567890',
            'photo' => UploadedFile::fake()->image('dompet.jpg'),
            'terms_accepted' => '1',
        ], $overrides);
    }
}
