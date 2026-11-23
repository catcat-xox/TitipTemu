<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use App\Models\User;
use Database\Seeders\WandiMissingStatusDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WandiMissingStatusDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_six_fictional_missing_reports_per_active_status_for_wandi(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['name' => 'wandi saputra', 'phone' => '081234567890']);
        Admin::factory()->create();

        $this->seed(WandiMissingStatusDemoSeeder::class);

        $this->assertSame(36, MissingReport::query()->where('user_id', $owner->id)->count());

        foreach ([
            MissingReport::STATUS_PENDING,
            MissingReport::STATUS_APPROVED,
            MissingReport::STATUS_REJECTED,
            MissingReport::STATUS_FOUND,
            MissingReport::STATUS_ARCHIVED,
            MissingReport::STATUS_CANCELLED,
        ] as $status) {
            $this->assertSame(
                6,
                MissingReport::query()->where('user_id', $owner->id)->where('status', $status)->count(),
                "Jumlah laporan kehilangan status {$status} harus enam."
            );
        }

        $this->assertSame(0, MissingReport::query()->where('status', MissingReport::STATUS_FOUND_PENDING)->count());
        $this->assertSame(48, MissingReportStatusHistory::query()->count());

        $found = MissingReport::query()->where('status', MissingReport::STATUS_FOUND)->firstOrFail();
        $this->assertNotNull($found->found_at);
        $this->assertTrue($found->isPublic());
        Storage::disk('local')->assertExists($found->photo_path);
        $this->assertDatabaseHas('missing_report_status_histories', [
            'missing_report_id' => $found->id,
            'action' => 'found_by_owner',
            'from_status' => MissingReport::STATUS_APPROVED,
            'to_status' => MissingReport::STATUS_FOUND,
            'actor_id' => $owner->id,
        ]);

        $archived = MissingReport::query()->where('status', MissingReport::STATUS_ARCHIVED)->firstOrFail();
        $this->assertTrue($archived->found_at->lessThan(now()->subDays(30)));
        $this->assertNotNull($archived->archived_at);
        $this->assertTrue($archived->archiveDueAt()->lessThanOrEqualTo($archived->archived_at));
        $this->assertFalse($archived->isPublic());

        $rejected = MissingReport::query()->where('status', MissingReport::STATUS_REJECTED)->firstOrFail();
        $this->assertNotNull($rejected->latestRejection?->reason);

        $cancelled = MissingReport::query()->where('status', MissingReport::STATUS_CANCELLED)->firstOrFail();
        $this->assertDatabaseHas('missing_report_status_histories', [
            'missing_report_id' => $cancelled->id,
            'action' => 'cancel_by_owner',
            'from_status' => MissingReport::STATUS_PENDING,
            'to_status' => MissingReport::STATUS_CANCELLED,
            'actor_id' => $owner->id,
        ]);
    }

    public function test_seeder_is_idempotent(): void
    {
        Storage::fake('local');
        User::factory()->create(['name' => 'wandi saputra', 'phone' => '081234567890']);
        Admin::factory()->create();

        $this->seed(WandiMissingStatusDemoSeeder::class);
        $this->seed(WandiMissingStatusDemoSeeder::class);

        $this->assertSame(36, MissingReport::query()->count());
        $this->assertSame(48, MissingReportStatusHistory::query()->count());
    }

    public function test_seeder_skips_when_wandi_is_absent(): void
    {
        Storage::fake('local');

        $this->seed(WandiMissingStatusDemoSeeder::class);

        $this->assertSame(0, MissingReport::query()->count());
    }
}
