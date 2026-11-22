<?php

namespace Tests\Feature;

use App\Models\LostReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_detail_offers_whatsapp_contact_without_exposing_phone_number(): void
    {
        $report = LostReport::factory()->approved()->create([
            'item_name' => 'Dompet Biru',
            'contact_phone' => '0812-3456-7890',
        ]);

        $this->get(route('reports.show', ['lost', $report]))
            ->assertOk()
            ->assertSee('Hubungi penemu via WhatsApp')
            ->assertSee(route('reports.contact', $report))
            ->assertDontSee('0812-3456-7890')
            ->assertDontSee('081234567890');
    }

    public function test_contact_route_redirects_to_whatsapp_with_normalized_number(): void
    {
        $report = LostReport::factory()->approved()->create([
            'contact_phone' => '+62 812-3456-7890',
        ]);

        $response = $this->get(route('reports.contact', $report));

        $response->assertStatus(302);
        $this->assertStringStartsWith(
            'https://wa.me/6281234567890?text=',
            (string) $response->headers->get('Location'),
        );
        $this->assertStringNotContainsString('6281234567890', (string) $this->get(route('reports.show', ['lost', $report]))->getContent());
    }

    public function test_contact_route_is_unavailable_for_pending_or_missing_phone(): void
    {
        $pending = LostReport::factory()->create(['contact_phone' => '081234567890']);
        $withoutPhone = LostReport::factory()->approved()->create(['contact_phone' => null]);

        $this->get(route('reports.contact', $pending))->assertNotFound();
        $this->get(route('reports.contact', $withoutPhone))->assertNotFound();
    }
}
