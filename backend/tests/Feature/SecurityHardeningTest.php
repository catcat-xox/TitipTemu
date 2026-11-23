<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_is_rate_limited(): void
    {
        $payload = ['email' => 'serang@example.test', 'password' => 'kata-sandi-salah'];

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post(route('login.store'), $payload)->assertStatus(302);
        }

        $this->post(route('login.store'), $payload)->assertStatus(429);
    }

    public function test_registration_is_rate_limited(): void
    {
        $payload = ['email' => 'daftar-serang@example.test'];

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post(route('register.store'), $payload)->assertStatus(302);
        }

        $this->post(route('register.store'), $payload)->assertStatus(429);
    }

    public function test_admin_login_is_rate_limited(): void
    {
        $payload = ['email' => 'admin-serang@example.test', 'password' => 'kata-sandi-salah'];

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post(route('admin.login.store'), $payload)->assertStatus(302);
        }

        $this->post(route('admin.login.store'), $payload)->assertStatus(429);
    }

    public function test_user_with_unknown_role_cannot_access_user_area(): void
    {
        $user = User::factory()->create(['role' => 'moderator']);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_private_report_photo_disables_browser_caching(): void
    {
        Storage::fake('local');

        $report = LostReport::factory()->approved()->create([
            'photo_path' => 'lost-reports/demo-privat.jpg',
        ]);
        Storage::disk('local')->put($report->photo_path, UploadedFile::fake()->image('demo.jpg')->getContent());

        $this->get(route('reports.photo', $report))
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=0, no-store, private')
            ->assertHeader('Pragma', 'no-cache');
    }
}
