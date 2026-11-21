<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_fictional_demo_user(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'name' => 'Demo User',
            'email' => 'demo.user@titiptemu.test',
        ]);

        $this->assertSame(1, User::query()->count());
    }

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::query()->count());
    }
}
