<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<html lang="id">', false);
        $response->assertSee('Barang yang ditemukan,');
        $response->assertSee('lebih mudah kembali.');
        $response->assertSee('Informasi publik dapat dilihat tanpa akun.');
        $response->assertSee('Nama pemilik dan foto bersama hanya diakses untuk verifikasi.');
        $response->assertSee('Lewati ke konten utama');
        $response->assertSee('id="konten"', false);
    }

    public function test_home_page_uses_the_main_layout(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-label="Navigasi utama"', false);
        $response->assertSee('class="site-footer"', false);
        $response->assertSee('TitipTemu');
    }

    public function test_home_shows_four_latest_lost_reports_and_drops_heavy_sections(): void
    {
        $owner = User::factory()->create();

        foreach (range(1, 5) as $day) {
            $report = LostReport::factory()
                ->for($owner)
                ->approved()
                ->create(['item_name' => 'Barang Hilang '.$day]);

            $report->forceFill(['created_at' => now()->subDays($day)])->save();
        }

        $response = $this->get('/')->assertOk();

        foreach (range(1, 4) as $day) {
            $response->assertSee('Barang Hilang '.$day);
        }
        $response->assertDontSee('Barang Hilang 5');
        $response->assertSee('Barang hilang yang baru dilaporkan');
        $response->assertSee(route('reports.index', 'lost'), false);
        $response->assertSee('<div class="mt-auto pt-2">', false);
        $response->assertSee('Lihat detail');
        $response->assertSee('Lihat selengkapnya');
        $response->assertDontSee('Barang yang baru kembali');
        $response->assertDontSee('Dua daftar, satu alur yang jelas');
        $response->assertDontSee('Peran pengguna');
        $response->assertDontSee('Guest');
        $response->assertDontSee('Dikembangkan dengan Laravel dan MySQL');
        $response->assertSee('Dikembangkan oleh Kelompok 6 Opensource');
    }

    public function test_only_one_inventory_link_is_active_on_each_list_page(): void
    {
        $lost = $this->get(route('reports.index', 'lost'))->assertOk();
        $found = $this->get(route('reports.index', 'found'))->assertOk();

        $this->assertSame(1, substr_count((string) $lost->getContent(), 'class="nav-link active"'));
        $this->assertSame(1, substr_count((string) $found->getContent(), 'class="nav-link active"'));
    }
}
