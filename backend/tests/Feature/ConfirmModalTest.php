<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_pages_use_shared_confirmation_modal_without_native_dialogs(): void
    {
        $user = User::factory()->create();
        MissingReport::factory()->for($user)->approved()->create(['item_name' => 'Dompet uji']);

        foreach ([route('missing-reports.mine'), route('user.dashboard')] as $uri) {
            $html = $this->actingAs($user)->get($uri)->assertOk()->getContent();

            $this->assertStringNotContainsString('onsubmit="return confirm(', $html, "Dialog bawaan masih ada di {$uri}.");
            $this->assertStringNotContainsString("confirm('", $html, "Pemanggilan confirm() masih ada di {$uri}.");
            $this->assertStringContainsString('id="ttConfirmModal"', $html, "Modal bersama tidak termuat di {$uri}.");
            $this->assertStringContainsString(
                'data-tt-confirm="Batalkan laporan kehilangan ini? Laporan tidak lagi ditampilkan."',
                $html
            );
            $this->assertStringContainsString(
                'data-tt-confirm="Tandai barang ini sudah ditemukan? Laporan akan dipindah ke Barang Sudah Ditemukan selama 30 hari."',
                $html
            );
            $this->assertStringContainsString('data-tt-confirm-variant="primary"', $html);
            $this->assertStringContainsString('data-tt-confirm-variant="success"', $html);
        }
    }

    public function test_admin_pages_use_shared_confirmation_modal_without_native_dialogs(): void
    {
        $admin = Admin::factory()->create();
        $owner = User::factory()->create();
        $missing = MissingReport::factory()->for($owner)->create(['item_name' => 'Kunci uji']);
        $lost = LostReport::factory()->for($owner)->returnedAt(now())->create(['item_name' => 'Buku uji']);

        $pages = [
            route('admin.dashboard'),
            route('admin.reports.show', $lost),
            route('admin.reports.missing.show', $missing),
        ];

        foreach ($pages as $uri) {
            $html = $this->actingAs($admin, 'admin')->get($uri)->assertOk()->getContent();

            $this->assertStringNotContainsString('onsubmit="return confirm(', $html, "Dialog bawaan masih ada di {$uri}.");
            $this->assertStringNotContainsString("confirm('", $html, "Pemanggilan confirm() masih ada di {$uri}.");
            $this->assertStringContainsString('id="ttConfirmModal"', $html, "Modal bersama tidak termuat di {$uri}.");
            $this->assertStringContainsString('data-tt-confirm-variant="danger"', $html);
        }

        $html = $this->actingAs($admin, 'admin')->get(route('admin.reports.missing.show', $missing))->assertOk()->getContent();
        $this->assertStringContainsString(
            'data-tt-confirm="Hapus permanen laporan kehilangan ini beserta foto dan riwayatnya? Tindakan ini tidak dapat dibatalkan."',
            $html
        );
        $this->assertStringContainsString('data-tt-confirm-title="Hapus permanen laporan kehilangan?"', $html);
    }
}
