<?php

namespace Database\Factories;

use App\Models\MissingReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MissingReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'item_name' => 'Barang '.fake()->word(),
            'category' => 'Lainnya',
            'description' => fake()->sentence(14),
            'lost_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'location' => fake()->city(),
            'contact_phone' => '0812'.fake()->numerify('#######'),
            'photo_path' => 'missing-reports/'.fake()->uuid().'.jpg',
            'status' => MissingReport::STATUS_PENDING,
            'terms_accepted_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => MissingReport::STATUS_APPROVED,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => MissingReport::STATUS_REJECTED,
        ]);
    }

    public function foundPending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => MissingReport::STATUS_FOUND_PENDING,
        ]);
    }

    public function foundAt($date): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => MissingReport::STATUS_FOUND,
            'found_at' => $date,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => MissingReport::STATUS_ARCHIVED,
            'archived_at' => now(),
        ]);
    }
}
