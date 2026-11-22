<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LostReportFactory extends Factory
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
            'photo_path' => 'lost-reports/'.fake()->uuid().'.jpg',
            'status' => 'pending',
            'terms_accepted_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'approved']);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'rejected']);
    }

    public function returnedAt($date): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'returned',
            'returned_at' => $date,
        ]);
    }
}
