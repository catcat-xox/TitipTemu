<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_public_lists_and_details(): void
    {
        $lost = LostReport::factory()->approved()->create();
        $found = LostReport::factory()->returnedAt(now()->subDays(2))->create();

        $this->get(route('reports.index', 'lost'))
            ->assertOk()
            ->assertSee($lost->item_name);
        $this->get(route('reports.index', 'found'))
            ->assertOk()
            ->assertSee($found->item_name);
        $this->get(route('reports.show', ['lost', $lost]))
            ->assertOk()
            ->assertSee($lost->item_name);
        $this->get(route('reports.show', ['found', $found]))
            ->assertOk()
            ->assertSee($found->item_name);
    }

    public function test_invalid_report_type_returns_not_found(): void
    {
        $this->get('/barang/selesai')->assertNotFound();
        $this->get('/barang/selesai/1')->assertNotFound();
    }

    public function test_non_public_reports_are_hidden_from_lists_and_details(): void
    {
        foreach (['pending', 'rejected', 'cancelled', 'archived'] as $status) {
            $report = LostReport::factory()->create(['status' => $status]);

            $this->get(route('reports.index', 'lost'))
                ->assertOk()
                ->assertDontSee($report->item_name);
            $this->get(route('reports.show', ['lost', $report]))->assertNotFound();
        }
    }

    public function test_returned_reports_older_than_thirty_days_are_hidden(): void
    {
        $stale = LostReport::factory()->returnedAt(now()->subDays(31))->create();
        $fresh = LostReport::factory()->returnedAt(now()->subDays(29))->create();

        $this->get(route('reports.index', 'found'))
            ->assertOk()
            ->assertDontSee($stale->item_name)
            ->assertSee($fresh->item_name);
        $this->get(route('reports.show', ['found', $stale]))->assertNotFound();
    }

    public function test_approved_report_is_not_available_on_found_list(): void
    {
        $report = LostReport::factory()->approved()->create();

        $this->get(route('reports.index', 'found'))->assertDontSee($report->item_name);
        $this->get(route('reports.show', ['found', $report]))->assertNotFound();
    }

    public function test_public_search_and_filters_work(): void
    {
        $match = LostReport::factory()->approved()->create([
            'item_name' => 'Dompet kulit',
            'category' => 'Dompet & Uang',
            'location' => 'Perpustakaan',
            'lost_at' => now()->subDays(3),
        ]);
        $other = LostReport::factory()->approved()->create([
            'item_name' => 'Kunci motor',
            'category' => 'Kunci',
            'location' => 'Gedung A',
            'lost_at' => now()->subDays(4),
        ]);

        $this->get(route('reports.index', ['lost', 'q' => 'Dompet']))
            ->assertSee($match->item_name)
            ->assertDontSee($other->item_name);
        $this->get(route('reports.index', ['lost', 'category' => 'Kunci']))
            ->assertSee($other->item_name)
            ->assertDontSee($match->item_name);
        $this->get(route('reports.index', ['lost', 'lost_date' => $match->lost_at->toDateString()]))
            ->assertSee($match->item_name)
            ->assertDontSee($other->item_name);
    }

    public function test_public_detail_does_not_expose_private_owner_data(): void
    {
        $owner = User::factory()->create([
            'nim_nip' => 'RAHASIA-123',
            'email' => 'private-owner@example.test',
            'faculty' => 'Fakultas Rahasia',
            'study_program' => 'Prodi Rahasia',
        ]);
        $report = LostReport::factory()->approved()->for($owner)->create();

        $this->get(route('reports.show', ['lost', $report]))
            ->assertOk()
            ->assertDontSee('RAHASIA-123')
            ->assertDontSee('private-owner@example.test')
            ->assertDontSee('Fakultas Rahasia')
            ->assertDontSee('Prodi Rahasia');
    }
}
