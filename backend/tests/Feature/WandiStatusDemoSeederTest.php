<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use App\Models\ReturnConfirmation;
use App\Models\User;
use Database\Seeders\WandiStatusDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WandiStatusDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_six_fictional_reports_per_status_for_wandi(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['name' => 'wandi saputra', 'phone' => '081234567890']);
        Admin::factory()->create();

        $this->seed(WandiStatusDemoSeeder::class);

        $this->assertSame(42, LostReport::query()->where('user_id', $owner->id)->count());

        foreach ([
            'pending',
            'approved',
            'return_pending',
            'rejected',
            'returned',
            'archived',
            'cancelled',
        ] as $status) {
            $this->assertSame(
                6,
                LostReport::query()->where('user_id', $owner->id)->where('status', $status)->count(),
                "Jumlah laporan status {$status} harus enam."
            );
        }

        $this->assertSame(18, ReturnConfirmation::query()->count());
        $this->assertSame(66, ReportStatusHistory::query()->count());

        $returned = LostReport::query()->where('status', 'returned')->firstOrFail();
        $this->assertNotNull($returned->returned_at);
        $this->assertTrue($returned->isPublic());
        Storage::disk('local')->assertExists($returned->photo_path);

        $archived = LostReport::query()->where('status', 'archived')->firstOrFail();
        $this->assertTrue($archived->returned_at->lessThan(now()->subDays(30)));
        $this->assertNotNull($archived->archived_at);
        $this->assertFalse($archived->isPublic());

        $rejected = LostReport::query()->where('status', 'rejected')->firstOrFail();
        $this->assertNotNull($rejected->latestRejection?->reason);
    }

    public function test_seeder_is_idempotent(): void
    {
        Storage::fake('local');
        User::factory()->create(['name' => 'wandi saputra', 'phone' => '081234567890']);
        Admin::factory()->create();

        $this->seed(WandiStatusDemoSeeder::class);
        $this->seed(WandiStatusDemoSeeder::class);

        $this->assertSame(42, LostReport::query()->count());
        $this->assertSame(18, ReturnConfirmation::query()->count());
        $this->assertSame(66, ReportStatusHistory::query()->count());
    }

    public function test_seeder_skips_when_wandi_is_absent(): void
    {
        Storage::fake('local');

        $this->seed(WandiStatusDemoSeeder::class);

        $this->assertSame(0, LostReport::query()->count());
    }
}
