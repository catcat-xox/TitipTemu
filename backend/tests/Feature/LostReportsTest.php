<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LostReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_public_found_reports(): void
    {
        $user = User::factory()->create();
        LostReport::create([
            'user_id' => $user->id,
            'item_name' => 'Dompet biru',
            'category' => 'Aksesori',
            'description' => 'Dompet tertinggal di area kampus.',
            'lost_at' => now()->subDay(),
            'location' => 'Perpustakaan',
            'photo_path' => 'lost-reports/dompet.jpg',
            'status' => 'approved',
            'terms_accepted_at' => now(),
        ]);

        $response = $this->get(route('reports.index', 'ditemukan'));

        $response->assertOk();
        $response->assertSee('Dompet biru');
    }

    public function test_pending_reports_are_not_public(): void
    {
        $user = User::factory()->create();
        LostReport::create([
            'user_id' => $user->id,
            'item_name' => 'Laporan privat',
            'category' => 'Lainnya',
            'description' => 'Laporan ini menunggu pemeriksaan Admin.',
            'lost_at' => now()->subDay(),
            'location' => 'Gedung A',
            'photo_path' => 'lost-reports/private.jpg',
            'status' => 'pending',
            'terms_accepted_at' => now(),
        ]);

        $this->get(route('reports.index', 'ditemukan'))
            ->assertOk()
            ->assertDontSee('Laporan privat');
    }

    public function test_authenticated_user_can_submit_a_lost_report(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'item_name' => 'Kartu mahasiswa',
            'category' => 'Dokumen',
            'description' => 'Kartu mahasiswa hilang di sekitar gedung kuliah.',
            'lost_at' => now()->subDay()->toDateString(),
            'location' => 'Gedung Kuliah',
            'contact_phone' => '081234567890',
            'photo' => UploadedFile::fake()->image('card.png'),
            'terms_accepted' => '1',
        ]);

        $response->assertRedirect(route('reports.mine'));
        $this->assertDatabaseHas('lost_reports', [
            'user_id' => $user->id,
            'item_name' => 'Kartu mahasiswa',
            'status' => 'pending',
        ]);
        Storage::disk('local')->assertExists(LostReport::first()->photo_path);
    }

    public function test_report_form_requires_photo_and_terms(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('reports.store'), [
            'item_name' => 'Kartu mahasiswa',
            'category' => 'Dokumen',
            'description' => 'Kartu mahasiswa hilang di sekitar gedung kuliah.',
            'lost_at' => now()->subDay()->toDateString(),
            'location' => 'Gedung Kuliah',
        ])->assertSessionHasErrors(['contact_phone', 'photo', 'terms_accepted']);
    }
}
