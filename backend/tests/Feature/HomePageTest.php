<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_renders_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<html lang="id">', false);
        $response->assertSee('Barang yang hilang,');
        $response->assertSee('lebih mudah kembali.');
        $response->assertSee('Informasi publik dapat dilihat tanpa akun.');
        $response->assertSee('Foto bukti pengembalian tetap privat.');
        $response->assertSee('Lewati ke konten utama');
        $response->assertSee('id="konten"', false);
    }

    public function test_home_page_uses_the_main_layout(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-label="Navigasi utama"', false);
        $response->assertSee('class="site-footer"', false);
        $response->assertSee('TitipTemu');
    }
}
