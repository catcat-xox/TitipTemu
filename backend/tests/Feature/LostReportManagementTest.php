<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LostReportManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_report_management_routes(): void
    {
        $report = LostReport::factory()->create();

        $this->get(route('reports.create'))->assertRedirect(route('login'));
        $this->get(route('reports.mine'))->assertRedirect(route('login'));
        $this->get(route('reports.edit', $report))->assertRedirect(route('login'));
        $this->put(route('reports.update', $report), [])->assertRedirect(route('login'));
        $this->post(route('reports.cancel', $report))->assertRedirect(route('login'));
        $this->post(route('reports.resubmit', $report))->assertRedirect(route('login'));
    }

    public function test_only_owner_can_open_edit_page(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->create();
        $stranger = User::factory()->create();

        $this->actingAs($owner)->get(route('reports.edit', $report))->assertOk();
        $this->actingAs($stranger)->get(route('reports.edit', $report))->assertForbidden();
    }

    public function test_approved_report_cannot_be_edited_or_cancelled(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->approved()->create();

        $this->actingAs($owner)->get(route('reports.edit', $report))->assertForbidden();
        $this->actingAs($owner)->put(route('reports.update', $report), $this->payload())->assertForbidden();
        $this->actingAs($owner)->post(route('reports.cancel', $report))->assertForbidden();
        $this->assertDatabaseHas('lost_reports', ['id' => $report->id, 'status' => 'approved']);
    }

    public function test_owner_can_update_pending_report_and_upload_a_new_photo(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->create();

        $this->actingAs($owner)->put(route('reports.update', $report), $this->payload([
            'item_name' => 'Dompet hitam',
            'photo' => UploadedFile::fake()->image('dompet.png'),
        ]))->assertRedirect(route('reports.mine'));

        $report->refresh();
        $this->assertSame('Dompet hitam', $report->item_name);
        $this->assertSame('pending', $report->status);
        Storage::disk('local')->assertExists($report->photo_path);
    }

    public function test_update_keeps_existing_photo_when_no_new_file_is_uploaded(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->create([
            'photo_path' => 'lost-reports/foto-lama.jpg',
        ]);

        $this->actingAs($owner)->put(route('reports.update', $report), $this->payload([
            'location' => 'Gedung B',
        ]))->assertRedirect(route('reports.mine'));

        $this->assertSame('Gedung B', $report->fresh()->location);
        $this->assertSame('lost-reports/foto-lama.jpg', $report->fresh()->photo_path);
    }

    public function test_update_validates_required_fields_and_photo(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->create();

        $this->actingAs($owner)->put(route('reports.update', $report), ['item_name' => '', 'contact_phone' => ''])
            ->assertSessionHasErrors(['item_name', 'category', 'description', 'lost_at', 'location', 'contact_phone']);
        $this->actingAs($owner)->put(route('reports.update', $report), $this->payload([
            'photo' => UploadedFile::fake()->image('foto.gif'),
        ]))->assertSessionHasErrors('photo');
        $this->actingAs($owner)->put(route('reports.update', $report), $this->payload([
            'photo' => UploadedFile::fake()->create('besar.jpg', 6000, 'image/jpeg'),
        ]))->assertSessionHasErrors('photo');
    }

    public function test_owner_can_cancel_pending_report(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->create();

        $this->actingAs($owner)->post(route('reports.cancel', $report))
            ->assertRedirect(route('reports.mine'));

        $this->assertSame('cancelled', $report->fresh()->status);
        $this->get(route('reports.index', 'found'))->assertDontSee($report->item_name);
    }

    public function test_rejected_report_can_be_resubmitted_without_changes(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->rejected()->create();

        $this->actingAs($owner)->get(route('reports.edit', $report))->assertOk();
        $this->actingAs($owner)->post(route('reports.resubmit', $report))
            ->assertRedirect(route('reports.mine'));

        $this->assertSame('pending', $report->fresh()->status);
    }

    public function test_updating_rejected_report_resubmits_it(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->rejected()->create();

        $this->actingAs($owner)->put(route('reports.update', $report), $this->payload([
            'description' => 'Deskripsi yang sudah diperbaiki sesuai catatan Admin.',
        ]))->assertRedirect(route('reports.mine'));

        $this->assertSame('pending', $report->fresh()->status);
    }

    public function test_pending_report_only_becomes_public_after_approval(): void
    {
        $report = LostReport::factory()->create();

        $this->get(route('reports.index', 'found'))->assertDontSee($report->item_name);
        $this->get(route('reports.show', ['found', $report]))->assertNotFound();

        $report->update(['status' => 'approved']);

        $this->get(route('reports.index', 'found'))->assertSee($report->item_name);
        $this->get(route('reports.show', ['found', $report]))->assertOk();
    }

    public function test_management_detail_link_uses_found_type_and_button_style(): void
    {
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->approved()->create([
            'item_name' => 'Dompet dengan detail',
        ]);

        $html = $this->actingAs($owner)->get(route('reports.mine'))->assertOk()->getContent();
        $detailUrl = route('reports.show', ['found', $report]);

        $this->assertStringContainsString($detailUrl, $html);
        $this->assertStringNotContainsString(route('reports.show', ['lost', $report]), $html);
        $this->assertStringContainsString(
            '<a class="btn btn-sm btn-outline-primary" href="'.$detailUrl.'">Detail</a>',
            $html
        );
        $this->assertStringNotContainsString('btn btn-sm btn-link', $html);
        $this->get($detailUrl)->assertOk();
    }

    public function test_user_without_verified_email_can_submit_a_report(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->post(route('reports.store'), $this->payload([
            'photo' => UploadedFile::fake()->image('dompet.jpg'),
            'terms_accepted' => '1',
        ]))->assertRedirect(route('reports.mine'));

        $this->assertDatabaseHas('lost_reports', [
            'user_id' => $user->id,
            'item_name' => 'Kartu mahasiswa',
            'status' => 'pending',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'item_name' => 'Kartu mahasiswa',
            'category' => 'Dokumen',
            'description' => 'Kartu mahasiswa hilang di sekitar gedung kuliah.',
            'lost_at' => now()->subDay()->toDateString(),
            'location' => 'Gedung Kuliah',
            'contact_phone' => '081234567890',
        ], $overrides);
    }
}
