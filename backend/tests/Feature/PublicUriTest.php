<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\MissingReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicUriTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_route_names_generate_indonesian_uris(): void
    {
        $this->assertStringEndsWith('/barang/hilang', route('reports.index', ['type' => 'hilang']));
        $this->assertStringEndsWith('/barang/ditemukan', route('reports.index', ['type' => 'ditemukan']));
        $this->assertStringEndsWith('/barang/hilang/7', route('reports.show', ['type' => 'hilang', 'report' => 7]));
        $this->assertStringEndsWith('/barang/ditemukan/9', route('reports.show', ['type' => 'ditemukan', 'report' => 9]));
    }

    public function test_new_uris_render_the_correct_public_lists(): void
    {
        MissingReport::factory()->approved()->create(['item_name' => 'Dompet hilang unik']);
        LostReport::factory()->approved()->create(['item_name' => 'Topi temuan unik']);

        $this->get(route('reports.index', ['type' => 'hilang']))
            ->assertOk()
            ->assertSee('Dompet hilang unik')
            ->assertDontSee('Topi temuan unik');

        $this->get(route('reports.index', ['type' => 'ditemukan']))
            ->assertOk()
            ->assertSee('Topi temuan unik')
            ->assertDontSee('Dompet hilang unik');
    }

    public function test_legacy_index_uris_redirect_permanently_and_keep_query_strings(): void
    {
        $this->get('/barang/lost')
            ->assertStatus(301)
            ->assertLocation(route('reports.index', ['type' => 'hilang']));

        $this->get('/barang/found')
            ->assertStatus(301)
            ->assertLocation(route('reports.index', ['type' => 'ditemukan']));

        $response = $this->get('/barang/lost?status=sudah&q=Dompet')
            ->assertStatus(301);

        $location = $response->headers->get('Location');
        $this->assertStringStartsWith(route('reports.index', ['type' => 'hilang']).'?', $location);
        parse_str(parse_url($location, PHP_URL_QUERY), $query);
        $this->assertEqualsCanonicalizing(['status' => 'sudah', 'q' => 'Dompet'], $query);
    }

    public function test_legacy_show_uris_redirect_permanently_to_the_new_detail_uris(): void
    {
        $missing = MissingReport::factory()->approved()->create();
        $found = LostReport::factory()->approved()->create();

        $this->get('/barang/lost/'.$missing->id)
            ->assertStatus(301)
            ->assertLocation(route('reports.show', ['type' => 'hilang', 'report' => $missing->id]));

        $this->get('/barang/found/'.$found->id)
            ->assertStatus(301)
            ->assertLocation(route('reports.show', ['type' => 'ditemukan', 'report' => $found->id]));
    }

    public function test_unknown_public_type_is_still_not_found(): void
    {
        $this->get('/barang/selesai')->assertNotFound();
        $this->get('/barang/selesai/1')->assertNotFound();
    }
}
