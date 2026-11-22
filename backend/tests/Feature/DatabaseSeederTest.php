<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_fictional_demo_user_admin_and_lost_reports(): void
    {
        Storage::fake('local');
        $this->seed();

        $this->assertDatabaseHas('users', [
            'name' => 'Demo User',
            'email' => 'demo.user@titiptemu.test',
        ]);
        $this->assertDatabaseHas('admins', [
            'name' => 'Admin Demo',
            'email' => 'admin@titiptemu.test',
        ]);
        $this->assertDatabaseHas('lost_reports', [
            'item_name' => 'Dompet Kulit Coklat',
            'status' => 'approved',
        ]);

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, Admin::query()->count());
        $this->assertSame(10, LostReport::query()->count());
        Storage::disk('local')->assertExists('lost-reports/demo-01.svg');
    }

    public function test_database_seeder_is_idempotent(): void
    {
        Storage::fake('local');
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, Admin::query()->count());
        $this->assertSame(10, LostReport::query()->count());
    }
}
