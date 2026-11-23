<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class InterfaceRefinementTest extends TestCase
{
    use RefreshDatabase;

    public function test_eyebrows_are_kept_only_on_home(): void
    {
        $this->get('/')->assertOk()->assertSee('<span class="eyebrow', false);

        $adminLoginUri = '/'.ltrim((string) config('admin.login_uri'), '/');
        $guestPages = [
            route('reports.index', 'hilang'),
            route('reports.index', 'ditemukan'),
            route('guide'),
            route('login'),
            route('register'),
            $adminLoginUri,
        ];

        foreach ($guestPages as $uri) {
            $this->get($uri)->assertOk()
                ->assertDontSee('<span class="eyebrow', false, "Eyebrow masih tampil di {$uri}");
        }

        $user = User::factory()->create();
        $report = LostReport::factory()->for($user)->create();
        $userPages = [
            route('reports.create'),
            route('reports.mine'),
            route('reports.edit', $report),
            route('user.dashboard'),
        ];

        foreach ($userPages as $uri) {
            $this->actingAs($user)->get($uri)->assertOk()
                ->assertDontSee('<span class="eyebrow', false, "Eyebrow masih tampil di {$uri}");
        }
    }

    public function test_admin_search_subtitle_is_removed(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('admin.reports.search'))
            ->assertOk()
            ->assertSee('Cari laporan')
            ->assertDontSee('Cari laporan dari semua status berdasarkan barang');
    }

    public function test_admin_edit_page_has_no_eyebrow(): void
    {
        $admin = Admin::factory()->create();
        $report = LostReport::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('admin.reports.edit', $report))
            ->assertOk()
            ->assertDontSee('<span class="eyebrow', false);
    }

    public function test_account_page_starts_with_profile_card(): void
    {
        $user = User::factory()->create(['name' => 'Profil Utama']);

        $response = $this->actingAs($user)->get(route('user.dashboard'))->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('profile-card', $html);
        $this->assertStringNotContainsString('page-hero', $html);
        $this->assertStringNotContainsString('Akun saya', $html);
        $this->assertStringNotContainsString('Profil Anda dan aktivitas laporan barang temuan', $html);
        $this->assertStringNotContainsString('Buka halaman verifikasi', $html);
        $this->assertStringContainsString('Edit profil', $html);
        $this->assertStringContainsString('>Keluar</button>', $html);
        $this->assertStringNotContainsString('class="profile-form"', $html);
        $this->assertLessThan(
            strpos($html, 'Laporan saya'),
            strpos($html, 'profile-card'),
            'Kartu profil harus muncul sebelum daftar laporan.'
        );
    }

    public function test_account_report_tables_center_the_status_and_date_columns(): void
    {
        $user = User::factory()->create();
        MissingReport::factory()->for($user)->create(['item_name' => 'Kunci hilang']);
        LostReport::factory()->for($user)->create(['item_name' => 'Dompet ditemukan']);

        $html = $this->actingAs($user)->get(route('user.dashboard'))->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, '<th scope="col" class="text-center">Status</th>'));
        $this->assertSame(1, substr_count($html, '<th scope="col" class="text-center">Tanggal hilang</th>'));
        $this->assertSame(1, substr_count($html, '<th scope="col" class="text-center">Tanggal ditemukan</th>'));
        $this->assertSame(4, substr_count($html, '<td class="text-center">'));
    }

    public function test_registration_form_has_required_phone_field_with_plain_label(): void
    {
        $html = $this->get(route('register'))->assertOk()->getContent();

        $this->assertStringContainsString('<label class="form-label" for="phone">No. HP</label>', $html);
        $this->assertStringNotContainsString('<label class="form-label" for="phone">No. HP <span', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="phone"[^>]*required/', $html);
        $this->assertStringContainsString('name="phone"', $html);
        $this->assertStringContainsString('type="tel"', $html);
        $this->assertStringContainsString('(opsional)', $html);
        $this->assertStringContainsString('(wajib diisi mahasiswa)', $html);
        $this->assertStringNotContainsString('form-text">Wajib diisi mahasiswa.', $html);
        $this->assertStringNotContainsString('Boleh dikosongkan', $html);
    }

    public function test_account_form_has_required_phone_field_with_plain_label(): void
    {
        $user = User::factory()->create(['phone' => '0812-3456-7890']);

        $dashboardHtml = $this->actingAs($user)->get(route('user.dashboard'))->assertOk()->getContent();
        $html = $this->actingAs($user)->get(route('user.profile.edit'))->assertOk()->getContent();

        $this->assertStringContainsString('No. HP: 0812-3456-7890', $dashboardHtml);
        $this->assertStringContainsString('Edit profil', $dashboardHtml);
        $this->assertStringContainsString('<label class="form-label" for="phone">No. HP</label>', $html);
        $this->assertStringNotContainsString('<label class="form-label" for="phone">No. HP <span', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="phone"[^>]*required/', $html);
        $this->assertStringContainsString('value="0812-3456-7890"', $html);
        $this->assertStringContainsString('(opsional)', $html);
        $this->assertStringContainsString('(wajib diisi mahasiswa)', $html);
        $this->assertStringNotContainsString('Kosongkan jika tidak ingin mengganti foto profil.', $html);
    }

    public function test_profile_edit_page_is_restricted_to_authenticated_users(): void
    {
        $this->get(route('user.profile.edit'))->assertRedirect(route('login'));

        $user = User::factory()->create(['name' => 'Pemilik Profil']);

        $this->actingAs($user)->get(route('user.profile.edit'))
            ->assertOk()
            ->assertSee('Ubah profil')
            ->assertSee('Simpan profil');
    }

    public function test_report_card_detail_buttons_use_bottom_aligned_wrapper(): void
    {
        MissingReport::factory()->approved()->create([
            'description' => 'Deskripsi pendek.',
        ]);
        MissingReport::factory()->approved()->create([
            'description' => str_repeat('Deskripsi panjang untuk menguji keseragaman tinggi kartu. ', 8),
        ]);
        LostReport::factory()->approved()->create();

        foreach (['/', route('reports.index', 'hilang'), route('reports.index', 'ditemukan')] as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();

            $this->assertStringContainsString(
                '<div class="mt-auto pt-2">',
                $html,
                "Tombol Lihat detail harus dibungkus pendorong ke bawah di {$uri}."
            );
            $this->assertStringNotContainsString(
                'class="btn btn-sm btn-outline-primary mt-2"',
                $html,
                "Tombol Lihat detail tidak boleh memakai margin atas tetap di {$uri}."
            );
        }
    }

    public function test_public_list_pages_start_with_status_cards_then_search_and_use_page_specific_labels(): void
    {
        MissingReport::factory()->approved()->create();
        MissingReport::factory()->foundAt(now()->subDays(2))->create();
        LostReport::factory()->approved()->create();
        LostReport::factory()->returnedAt(now()->subDays(2))->create();

        foreach ([
            'hilang' => [
                'search' => 'Cari Barang Hilang',
                'labels' => ['Barang Belum Ditemukan', 'Barang Sudah Ditemukan'],
            ],
            'ditemukan' => [
                'search' => 'Cari Barang Ditemukan',
                'labels' => ['Barang Belum Kembali', 'Barang Sudah Kembali'],
            ],
        ] as $type => $expected) {
            $html = $this->get(route('reports.index', $type))->assertOk()->getContent();

            $this->assertStringNotContainsString('page-hero', $html);
            $this->assertStringNotContainsString('page-intro', $html);
            $this->assertStringNotContainsString('Cari barang temuan yang telah disetujui', $html);
            $this->assertStringNotContainsString('Lihat barang yang sudah dikembalikan', $html);
            $this->assertStringContainsString('<label class="form-label" for="q">'.$expected['search'].'</label>', $html);

            foreach ($expected['labels'] as $label) {
                $this->assertStringContainsString($label, $html);
            }
            foreach (['belum', 'sudah'] as $option) {
                $this->assertStringContainsString(
                    route('reports.index', [$type, 'status' => $option]),
                    $html,
                );
            }
            $this->assertStringContainsString('<input type="hidden" name="status" value="belum">', $html);

            $statePosition = strpos($html, 'class="state-card');
            $searchPosition = strpos($html, 'reports-filter-panel');
            $cardPosition = strpos($html, 'report-card');
            $this->assertNotFalse($statePosition);
            $this->assertNotFalse($searchPosition);
            $this->assertNotFalse($cardPosition);
            $this->assertLessThan($searchPosition, $statePosition, "Card status harus tampil sebelum pencarian ({$type}).");
            $this->assertLessThan($cardPosition, $searchPosition, "Panel pencarian harus tampil sebelum kartu laporan ({$type}).");
        }
    }

    public function test_public_navbar_uses_report_dropdown_and_icon_account_actions(): void
    {
        $user = User::factory()->create(['name' => 'Navbar Pengguna']);
        $html = $this->actingAs($user)->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('id="buatLaporanMenu"', $html);
        $this->assertStringContainsString('Buat Laporan', $html);
        $this->assertStringContainsString('data-bs-toggle="dropdown"', $html);
        $this->assertStringContainsString('<a class="dropdown-item" href="'.route('missing-reports.create').'">', $html);
        $this->assertStringContainsString('Laporkan Kehilangan Barang', $html);
        $this->assertStringContainsString('<a class="dropdown-item" href="'.route('reports.create').'">', $html);
        $this->assertStringContainsString('Laporkan Barang Temuan', $html);
        $this->assertStringContainsString('aria-label="Akun"', $html);
        $this->assertStringNotContainsString('aria-label="Keluar"', $html);
        $this->assertStringNotContainsString('Halo, Navbar Pengguna', $html);
        $this->assertStringNotContainsString('btn btn-outline-secondary">Akun', $html);
        $this->assertStringNotContainsString(route('logout'), $html);
        $this->assertLessThan(
            strpos($html, 'id="buatLaporanMenu"'),
            strpos($html, 'aria-label="Akun"'),
            'Tombol Akun harus berada sebelum dropdown Buat Laporan.'
        );

        $admin = Admin::factory()->create(['name' => 'Admin Navbar']);
        $adminHtml = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))->assertOk()->getContent();
        $this->assertStringContainsString('Halo, Admin Navbar', $adminHtml);
    }

    public function test_flash_notices_render_as_floating_toasts_in_every_layout(): void
    {
        $this->withSession(['success' => 'Laporan berhasil dikirim.'])->get('/')
            ->assertOk()
            ->assertSee('tt-toast-stack', false)
            ->assertSee('data-tt-toast-lifetime="3000"', false)
            ->assertSee('tt-toast--success', false)
            ->assertSee('Laporan berhasil dikirim.');

        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['status' => 'Anda telah keluar.'])->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('tt-toast--info', false)
            ->assertSee('Anda telah keluar.');

        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin')->withSession(['success' => 'Pengembalian disetujui.'])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('tt-toast--success', false);

        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        $adminLoginUri = '/'.ltrim((string) config('admin.login_uri'), '/');
        $this->withSession(['error' => 'Email Admin tidak sesuai.'])->get($adminLoginUri)
            ->assertOk()
            ->assertSee('tt-toast--error', false)
            ->assertSee('Email Admin tidak sesuai.');
    }

    public function test_validation_errors_render_as_floating_toast(): void
    {
        $this->from('/register')->post('/register', [])->assertRedirect('/register');

        $this->get('/register')
            ->assertOk()
            ->assertSee('tt-toast--error', false)
            ->assertSee('Periksa kembali data berikut:');
    }
}
