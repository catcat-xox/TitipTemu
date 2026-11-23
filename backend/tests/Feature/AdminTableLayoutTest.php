<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTableLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_one_action_dropdown_per_report(): void
    {
        $admin = Admin::factory()->create();
        $owner = User::factory()->create();
        MissingReport::factory()->for($owner)->approved()->create(['item_name' => 'Dompet uji']);

        $html = $this->actingAs($admin, 'admin')->get(route('admin.dashboard', ['status' => 'all']))
            ->assertOk()->getContent();

        $this->assertStringContainsString('data-tt-action-menu', $html);
        $this->assertStringContainsString('class="dropdown-menu dropdown-menu-end tt-action-menu"', $html);
        $this->assertStringContainsString('>Tinjau</a>', $html);
        $this->assertStringContainsString('>Edit</a>', $html);
        $this->assertStringContainsString('>Hapus</button>', $html);
        $this->assertStringNotContainsString('btn btn-sm btn-outline-secondary', $html);
        $this->assertStringNotContainsString('btn btn-sm btn-outline-danger', $html);
        $this->assertStringNotContainsString('<td class="text-center">', $html, 'Isi tabel Admin tidak boleh dirata-tengahkan.');
    }

    public function test_admin_table_headers_are_centered(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        MissingReport::factory()->for($user)->approved()->create(['item_name' => 'Kunci uji']);

        foreach ([route('admin.dashboard', ['status' => 'all']), route('admin.reports.search')] as $uri) {
            $html = $this->actingAs($admin, 'admin')->get($uri)->assertOk()->getContent();
            $this->assertAllTableHeadersCentered($html, $uri);
        }
    }

    public function test_user_table_headers_are_centered(): void
    {
        $user = User::factory()->create();
        MissingReport::factory()->for($user)->approved()->create(['item_name' => 'Kunci uji']);
        LostReport::factory()->for($user)->approved()->create(['item_name' => 'Buku uji']);

        foreach ([route('user.dashboard'), route('reports.mine'), route('missing-reports.mine')] as $uri) {
            $html = $this->actingAs($user)->get($uri)->assertOk()->getContent();
            $this->assertAllTableHeadersCentered($html, $uri);
        }
    }

    private function assertAllTableHeadersCentered(string $html, string $uri): void
    {
        preg_match_all('/<th scope="col"[^>]*>/', $html, $matches);

        $this->assertNotEmpty($matches[0], "Tidak ada header tabel di {$uri}.");

        foreach ($matches[0] as $header) {
            $this->assertStringContainsString(
                'class="text-center"',
                $header,
                "Header tabel belum rata tengah di {$uri}: {$header}"
            );
        }
    }
}
