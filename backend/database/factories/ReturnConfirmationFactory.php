<?php

namespace Database\Factories;

use App\Models\LostReport;
use App\Models\ReturnConfirmation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReturnConfirmationFactory extends Factory
{
    protected $model = ReturnConfirmation::class;

    public function definition(): array
    {
        return [
            'lost_report_id' => LostReport::factory(),
            'user_id' => User::factory(),
            'status' => 'pending',
            'attempt' => 1,
            'reported_returned_at' => now(),
            'proof_paths' => ['return-proofs/'.fake()->uuid().'.jpg'],
            'note' => null,
            'rejection_reason' => null,
            'admin_note' => null,
            'is_admin_resolution' => false,
            'reviewed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'approved']);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'rejected',
            'rejection_reason' => 'Bukti pengembalian perlu diperjelas.',
            'reviewed_at' => now(),
        ]);
    }
}
