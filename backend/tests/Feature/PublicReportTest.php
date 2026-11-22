<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_public_lists_and_details_for_both_report_types(): void
    {
        $missing = MissingReport::factory()->approved()->create(['item_name' => 'Kunci hilang']);
        $found = LostReport::factory()->approved()->create(['item_name' => 'Dompet ditemukan']);

        $this->get(route('reports.index', 'lost'))
            ->assertOk()
            ->assertSee($missing->item_name)
            ->assertDontSee($found->item_name);
        $this->get(route('reports.index', 'found'))
            ->assertOk()
            ->assertSee($found->item_name)
            ->assertDontSee($missing->item_name);
        $this->get(route('reports.show', ['lost', $missing]))
            ->assertOk()
            ->assertSee($missing->item_name);
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
            $missing = MissingReport::factory()->create([
                'status' => $status,
                'item_name' => 'Privat kehilangan '.$status,
            ]);
            $found = LostReport::factory()->create([
                'status' => $status,
                'item_name' => 'Privat temuan '.$status,
            ]);

            foreach (['lost', 'found'] as $type) {
                $this->get(route('reports.index', [$type, 'status' => 'belum']))
                    ->assertOk()
                    ->assertDontSee($missing->item_name)
                    ->assertDontSee($found->item_name);
                $this->get(route('reports.index', [$type, 'status' => 'sudah']))
                    ->assertOk()
                    ->assertDontSee($missing->item_name)
                    ->assertDontSee($found->item_name);
            }

            $this->get(route('reports.show', ['lost', $missing]))->assertNotFound();
            $this->get(route('reports.show', ['found', $found]))->assertNotFound();
        }
    }

    public function test_status_cards_switch_state_within_the_same_report_type(): void
    {
        $missingBelum = MissingReport::factory()->approved()->create(['item_name' => 'Kunci belum ditemukan']);
        $missingSudah = MissingReport::factory()->foundAt(now()->subDays(2))->create(['item_name' => 'Kunci sudah ditemukan']);
        $foundBelum = LostReport::factory()->approved()->create(['item_name' => 'Dompet belum kembali']);
        $foundSudah = LostReport::factory()->returnedAt(now()->subDays(2))->create(['item_name' => 'Dompet sudah kembali']);

        $this->get(route('reports.index', 'lost'))
            ->assertSee('Barang Belum Ditemukan')
            ->assertSee('Barang Sudah Ditemukan')
            ->assertSee(route('reports.index', ['lost', 'status' => 'belum']), false)
            ->assertSee(route('reports.index', ['lost', 'status' => 'sudah']), false)
            ->assertSee($missingBelum->item_name)
            ->assertDontSee($missingSudah->item_name)
            ->assertDontSee($foundBelum->item_name)
            ->assertDontSee($foundSudah->item_name);

        $this->get(route('reports.index', ['lost', 'status' => 'sudah']))
            ->assertSee($missingSudah->item_name)
            ->assertDontSee($missingBelum->item_name)
            ->assertDontSee($foundSudah->item_name);

        $this->get(route('reports.index', 'found'))
            ->assertSee('Barang Belum Kembali')
            ->assertSee('Barang Sudah Kembali')
            ->assertSee(route('reports.index', ['found', 'status' => 'belum']), false)
            ->assertSee(route('reports.index', ['found', 'status' => 'sudah']), false)
            ->assertSee($foundBelum->item_name)
            ->assertDontSee($foundSudah->item_name)
            ->assertDontSee($missingBelum->item_name)
            ->assertDontSee($missingSudah->item_name);

        $this->get(route('reports.index', ['found', 'status' => 'sudah']))
            ->assertSee($foundSudah->item_name)
            ->assertDontSee($foundBelum->item_name)
            ->assertDontSee($missingSudah->item_name);
    }

    public function test_sudah_lists_hide_reports_older_than_thirty_days(): void
    {
        $staleMissing = MissingReport::factory()->foundAt(now()->subDays(31))->create(['item_name' => 'Kunci lama']);
        $freshMissing = MissingReport::factory()->foundAt(now()->subDays(29))->create(['item_name' => 'Kunci baru']);
        $staleFound = LostReport::factory()->returnedAt(now()->subDays(31))->create(['item_name' => 'Dompet lama']);
        $freshFound = LostReport::factory()->returnedAt(now()->subDays(29))->create(['item_name' => 'Dompet baru']);

        $this->get(route('reports.index', ['lost', 'status' => 'sudah']))
            ->assertDontSee($staleMissing->item_name)
            ->assertSee($freshMissing->item_name);
        $this->get(route('reports.show', ['lost', $staleMissing]))->assertNotFound();

        $this->get(route('reports.index', ['found', 'status' => 'sudah']))
            ->assertDontSee($staleFound->item_name)
            ->assertSee($freshFound->item_name);
        $this->get(route('reports.show', ['found', $staleFound]))->assertNotFound();
    }

    public function test_approved_report_only_appears_in_the_belum_card_of_its_own_type(): void
    {
        $missing = MissingReport::factory()->approved()->create(['item_name' => 'Barang hilang unik']);
        $found = LostReport::factory()->approved()->create(['item_name' => 'Barang temuan unik']);

        foreach (['lost', 'found'] as $type) {
            $this->get(route('reports.index', [$type, 'status' => 'sudah']))
                ->assertDontSee($missing->item_name)
                ->assertDontSee($found->item_name);
        }

        $this->get(route('reports.show', ['found', $missing]))
            ->assertDontSee($missing->item_name);
        $this->get(route('reports.show', ['lost', $found]))
            ->assertDontSee($found->item_name);
    }

    public function test_public_search_and_filters_work_on_both_lists(): void
    {
        $missingMatch = MissingReport::factory()->approved()->create([
            'item_name' => 'Dompet kulit',
            'category' => 'Dompet & Uang',
            'location' => 'Perpustakaan',
            'lost_at' => now()->subDays(3),
        ]);
        $missingOther = MissingReport::factory()->approved()->create([
            'item_name' => 'Kunci motor',
            'category' => 'Kunci',
            'location' => 'Gedung A',
            'lost_at' => now()->subDays(4),
        ]);
        $foundMatch = LostReport::factory()->approved()->create([
            'item_name' => 'Topbiru kulit',
            'category' => 'Pakaian',
            'location' => 'Asrama',
            'lost_at' => now()->subDays(3),
        ]);
        $foundOther = LostReport::factory()->approved()->create([
            'item_name' => 'Botol minum',
            'category' => 'Lainnya',
            'location' => 'Kantin',
            'lost_at' => now()->subDays(4),
        ]);

        $this->get(route('reports.index', ['lost', 'q' => 'Dompet']))
            ->assertSee($missingMatch->item_name)
            ->assertDontSee($missingOther->item_name);
        $this->get(route('reports.index', ['lost', 'category' => 'Kunci']))
            ->assertSee($missingOther->item_name)
            ->assertDontSee($missingMatch->item_name);
        $this->get(route('reports.index', ['lost', 'lost_date' => $missingMatch->lost_at->toDateString()]))
            ->assertSee($missingMatch->item_name)
            ->assertDontSee($missingOther->item_name);

        $this->get(route('reports.index', ['found', 'q' => 'Topbiru']))
            ->assertSee($foundMatch->item_name)
            ->assertDontSee($foundOther->item_name);
        $this->get(route('reports.index', ['found', 'lost_date' => $foundMatch->lost_at->toDateString()]))
            ->assertSee($foundMatch->item_name)
            ->assertDontSee($foundOther->item_name);
    }

    public function test_public_list_pagination_uses_bootstrap_five_markup(): void
    {
        foreach (range(1, 13) as $number) {
            MissingReport::factory()->approved()->create(['item_name' => 'Barang Hilang '.$number]);
        }

        $this->get(route('reports.index', 'lost'))
            ->assertOk()
            ->assertSee('class="pagination"', false)
            ->assertSee('class="page-item', false)
            ->assertSee('class="page-link"', false)
            ->assertSee('rel="next"', false)
            ->assertDontSee('inline-flex items-center px-2 py-2', false);
    }

    public function test_public_detail_does_not_expose_private_owner_data_or_phone(): void
    {
        $owner = User::factory()->create([
            'nim_nip' => 'RAHASIA-123',
            'email' => 'private-owner@example.test',
            'faculty' => 'Fakultas Rahasia',
            'study_program' => 'Prodi Rahasia',
        ]);
        $missing = MissingReport::factory()->approved()->for($owner)->create([
            'contact_phone' => '0812-1111-2222',
        ]);
        $found = LostReport::factory()->approved()->for($owner)->create([
            'contact_phone' => '0812-3333-4444',
        ]);

        foreach ([['lost', $missing], ['found', $found]] as [$type, $report]) {
            $this->get(route('reports.show', [$type, $report]))
                ->assertOk()
                ->assertDontSee('RAHASIA-123')
                ->assertDontSee('private-owner@example.test')
                ->assertDontSee('Fakultas Rahasia')
                ->assertDontSee('Prodi Rahasia')
                ->assertDontSee('0812-1111-2222')
                ->assertDontSee('0812-3333-4444')
                ->assertDontSee('081211112222')
                ->assertDontSee('081233334444');
        }
    }
}
