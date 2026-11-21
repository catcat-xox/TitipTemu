<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed deterministic, fictional development data.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'demo.user@titiptemu.test'],
            [
                'name' => 'Demo User',
                'email_verified_at' => now(),
                'role' => User::ROLE_USER,
                'nim_nip' => 'DEMO-0001',
                'user_type' => User::TYPE_STUDENT,
                'faculty' => 'Fakultas Demo',
                'study_program' => 'Program Studi Demo',
                'password' => Hash::make('password'),
            ],
        );
    }
}
