<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_works_without_profile_photo(): void
    {
        $this->post(route('register.store'), $this->registrationPayload())
            ->assertRedirect(route('home'));

        $user = User::firstOrFail();

        $this->assertNull($user->profile_photo_path);
        $this->assertSame('0812-3456-7890', $user->phone);
    }

    public function test_registration_can_store_an_optional_profile_photo(): void
    {
        Storage::fake('local');

        $this->post(route('register.store'), $this->registrationPayload([
            'profile_photo' => UploadedFile::fake()->image('profil.jpg'),
        ]))->assertRedirect(route('home'));

        $path = User::firstOrFail()->profile_photo_path;

        $this->assertNotNull($path);
        $this->assertStringStartsWith('profile-photos/', $path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_owner_can_update_biodata_and_profile_photo_from_account(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $this->actingAs($user, 'web')->put(route('user.profile.update'), [
            'name' => 'Nama Setelah Diubah',
            'phone' => '0813-9876-5432',
            'nim_nip' => '1234567890',
            'user_type' => 'lecturer',
            'faculty' => 'Fakultas Teknik',
            'study_program' => '',
            'profile_photo' => UploadedFile::fake()->image('profil-baru.jpg'),
        ])->assertRedirect(route('user.dashboard'));

        $user->refresh();

        $this->assertSame('Nama Setelah Diubah', $user->name);
        $this->assertSame('0813-9876-5432', $user->phone);
        $this->assertSame('1234567890', $user->nim_nip);
        $this->assertSame('lecturer', $user->user_type);
        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('local')->assertExists($user->profile_photo_path);
    }

    public function test_owner_cannot_clear_required_phone_from_biodata(): void
    {
        $user = User::factory()->create(['phone' => '0812-3456-7890']);

        $response = $this->actingAs($user, 'web')->put(route('user.profile.update'), [
            'name' => $user->name,
            'phone' => '',
            'nim_nip' => $user->nim_nip,
            'user_type' => $user->user_type,
            'faculty' => $user->faculty,
            'study_program' => $user->study_program,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('phone');
        $this->assertSame('0812-3456-7890', $user->fresh()->phone);
    }

    public function test_profile_photo_is_private_to_its_owner(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['profile_photo_path' => 'profile-photos/demo.jpg']);
        Storage::disk('local')->put('profile-photos/demo.jpg', 'konten foto');

        $this->get(route('user.profile-photo'))->assertRedirect(route('login'));

        $other = User::factory()->create();
        $this->actingAs($other, 'web')->get(route('user.profile-photo'))
            ->assertNotFound();

        $this->actingAs($user, 'web')->get(route('user.profile-photo'))
            ->assertOk();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pendaftar Baru',
            'email' => 'pendaftar.baru@titiptemu.test',
            'phone' => '0812-3456-7890',
            'nim_nip' => '20260001',
            'user_type' => 'student',
            'faculty' => 'Fakultas Ilmu Komputer',
            'study_program' => 'Teknik Informatika',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ], $overrides);
    }
}
