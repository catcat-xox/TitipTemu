<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountActionMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_missing_report_uses_action_dropdown_with_visible_found_button(): void
    {
        $user = User::factory()->create();
        $report = MissingReport::factory()->for($user)->approved()->create([
            'item_name' => 'Dompet disetujui',
        ]);

        $html = $this->actingAs($user)->get(route('user.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('data-tt-action-menu', $html);
        $this->assertStringContainsString('class="dropdown-menu dropdown-menu-end tt-action-menu"', $html);
        $this->assertStringContainsString('class="btn btn-sm btn-outline-primary dropdown-toggle"', $html);
        $this->assertStringContainsString('class="dropdown-item" type="submit">Batalkan</button>', $html);
        $this->assertStringContainsString(
            'class="dropdown-item" href="'.route('missing-reports.mine').'">Lihat semua</a>',
            $html
        );

        // Aksi utama tetap terlihat di luar dropdown dan berada paling kanan.
        $this->assertStringContainsString(
            '<button class="btn btn-sm btn-primary" type="submit">Barang ditemukan</button>',
            $html
        );
        $this->assertStringNotContainsString(
            'class="dropdown-item" type="submit">Barang ditemukan</button>',
            $html
        );

        $dropdownPos = strpos($html, 'class="dropdown-menu dropdown-menu-end tt-action-menu"');
        $cancelPos = strpos($html, route('missing-reports.cancel', $report));
        $lihatPos = strpos($html, '>Lihat semua</a>', $dropdownPos);
        $foundPos = strpos($html, route('missing-reports.found', $report));

        $this->assertNotFalse($dropdownPos);
        $this->assertNotFalse($cancelPos);
        $this->assertNotFalse($lihatPos);
        $this->assertNotFalse($foundPos);
        $this->assertGreaterThan($dropdownPos, $cancelPos, 'Form Batalkan harus berada di dalam dropdown.');
        $this->assertLessThan($foundPos, $lihatPos, 'Lihat semua di dropdown harus mendahului tombol Barang ditemukan.');
    }

    public function test_non_approved_missing_report_keeps_single_lihat_semua_action(): void
    {
        $user = User::factory()->create();
        MissingReport::factory()->for($user)->create(['item_name' => 'Kunci menunggu']);

        $html = $this->actingAs($user)->get(route('user.dashboard'))->assertOk()->getContent();

        $this->assertStringNotContainsString('data-tt-action-menu', $html);
        $this->assertSame(
            1,
            substr_count($html, '<a class="btn btn-sm btn-outline-primary" href="'.route('missing-reports.mine').'">Lihat semua</a>')
        );
        $this->assertStringNotContainsString('Barang ditemukan', $html);
        $this->assertStringNotContainsString('>Batalkan</button>', $html);
    }

    public function test_found_report_table_still_uses_single_lihat_semua_action(): void
    {
        $user = User::factory()->create();
        LostReport::factory()->for($user)->approved()->create(['item_name' => 'Buku temuan']);

        $html = $this->actingAs($user)->get(route('user.dashboard'))->assertOk()->getContent();

        $this->assertStringNotContainsString('data-tt-action-menu', $html);
        $this->assertSame(
            1,
            substr_count($html, '<a class="btn btn-sm btn-outline-primary" href="'.route('reports.mine').'">Lihat semua</a>')
        );
    }
}
