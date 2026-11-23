<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\User;
use App\Support\MissingReportPhoto;
use Database\Seeders\MissingReportDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MissingReportDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_both_missing_report_states_without_touching_found_reports(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create([
            'email' => 'demo.user@titiptemu.test',
            'phone' => '081234567890',
        ]);
        $foundReport = LostReport::factory()->create(['item_name' => 'Data Lama']);

        $this->seed(MissingReportDemoSeeder::class);

        $this->assertSame(8, MissingReport::query()->where('user_id', $owner->id)->count());
        $this->assertSame(4, MissingReport::query()->where('status', MissingReport::STATUS_APPROVED)->count());
        $this->assertSame(2, MissingReport::query()->where('status', MissingReport::STATUS_FOUND_PENDING)->count());
        $this->assertSame(2, MissingReport::query()->where('status', MissingReport::STATUS_FOUND)->count());
        $this->assertSame(2, MissingReport::query()->publicFound()->count());
        $this->assertSame(0, MissingReport::query()->publicUnfound()->where('status', MissingReport::STATUS_FOUND)->count());
        $this->assertSame(1, LostReport::query()->count());
        $this->assertSame('Data Lama', $foundReport->fresh()->item_name);

        $report = MissingReport::query()->where('status', MissingReport::STATUS_FOUND)->firstOrFail();
        $this->assertStringStartsWith(MissingReportPhoto::DIRECTORY.'/', $report->photo_path);
        Storage::disk('local')->assertExists($report->photo_path);
    }

    public function test_seeder_is_idempotent(): void
    {
        Storage::fake('local');
        User::factory()->create(['email' => 'demo.user@titiptemu.test']);

        $this->seed(MissingReportDemoSeeder::class);
        $this->seed(MissingReportDemoSeeder::class);

        $this->assertSame(8, MissingReport::query()->count());
    }
}
