<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\MissingReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_found_report_detail_offers_whatsapp_contact_without_exposing_phone_number(): void
    {
        $report = LostReport::factory()->approved()->create([
            'item_name' => 'Dompet Biru',
            'contact_phone' => '0812-3456-7890',
        ]);

        $this->get(route('reports.show', ['ditemukan', $report]))
            ->assertOk()
            ->assertSee('Hubungi penemu via WhatsApp')
            ->assertSee(route('reports.contact', $report))
            ->assertDontSee('0812-3456-7890')
            ->assertDontSee('081234567890');
    }

    public function test_missing_report_detail_offers_whatsapp_contact_without_exposing_phone_number(): void
    {
        $report = MissingReport::factory()->approved()->create([
            'item_name' => 'Kunci Hilang',
            'contact_phone' => '0812-3456-7890',
        ]);

        $this->get(route('reports.show', ['hilang', $report]))
            ->assertOk()
            ->assertSee('Hubungi pemilik via WhatsApp')
            ->assertSee(route('missing-reports.contact', $report))
            ->assertDontSee('0812-3456-7890')
            ->assertDontSee('081234567890');
    }

    public function test_contact_routes_redirect_to_whatsapp_with_normalized_number(): void
    {
        $found = LostReport::factory()->approved()->create([
            'contact_phone' => '+62 812-3456-7890',
        ]);
        $missing = MissingReport::factory()->approved()->create([
            'contact_phone' => '+62 812-3456-7890',
        ]);

        foreach ([route('reports.contact', $found), route('missing-reports.contact', $missing)] as $uri) {
            $response = $this->get($uri);

            $response->assertStatus(302);
            $this->assertStringStartsWith(
                'https://wa.me/6281234567890?text=',
                (string) $response->headers->get('Location'),
            );
        }
    }

    public function test_contact_routes_are_unavailable_for_private_or_expired_reports(): void
    {
        $pendingFound = LostReport::factory()->create(['contact_phone' => '081234567890']);
        $withoutPhoneFound = LostReport::factory()->approved()->create(['contact_phone' => null]);
        $staleFound = LostReport::factory()->returnedAt(now()->subDays(31))->create();
        $pendingMissing = MissingReport::factory()->create(['contact_phone' => '081234567890']);
        $invalidPhoneMissing = MissingReport::factory()->approved()->create(['contact_phone' => 'bukan nomor']);
        $staleMissing = MissingReport::factory()->foundAt(now()->subDays(31))->create();

        $this->get(route('reports.contact', $pendingFound))->assertNotFound();
        $this->get(route('reports.contact', $withoutPhoneFound))->assertNotFound();
        $this->get(route('reports.contact', $staleFound))->assertNotFound();
        $this->get(route('missing-reports.contact', $pendingMissing))->assertNotFound();
        $this->get(route('missing-reports.contact', $invalidPhoneMissing))->assertNotFound();
        $this->get(route('missing-reports.contact', $staleMissing))->assertNotFound();
    }
}
