<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_and_registration_pages(): void
    {
        $this->get('/login')->assertOk()->assertSee('Selamat datang kembali');
        $this->get('/register')->assertOk()->assertSee('Mulai bersama TitipTemu');
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'User Baru',
            'email' => 'user.baru@example.test',
            'phone' => '0812-3456-7890',
            'nim_nip' => '20260001',
            'user_type' => 'student',
            'faculty' => 'Fakultas Demo',
            'study_program' => 'Program Studi Demo',
            'password' => 'password-kuat',
            'password_confirmation' => 'password-kuat',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'User Baru',
            'email' => 'user.baru@example.test',
            'phone' => '0812-3456-7890',
            'nim_nip' => '20260001',
            'user_type' => 'student',
            'faculty' => 'Fakultas Demo',
            'study_program' => 'Program Studi Demo',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'user.baru@example.test',
            'email_verified_at' => null,
        ]);
    }

    public function test_registration_rejects_invalid_input(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => '',
            'nim_nip' => '',
            'user_type' => 'student',
            'faculty' => '',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'email', 'phone', 'password']);
        $this->assertGuest();
    }

    public function test_registration_requires_phone(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'User Baru',
            'email' => 'user.baru@example.test',
            'nim_nip' => '20260001',
            'user_type' => 'student',
            'faculty' => 'Fakultas Demo',
            'study_program' => 'Program Studi Demo',
            'password' => 'password-kuat',
            'password_confirmation' => 'password-kuat',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_registration_rejects_invalid_phone_format(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'User Baru',
            'email' => 'user.baru@example.test',
            'phone' => 'nomor yang bukan angka',
            'nim_nip' => '20260001',
            'user_type' => 'student',
            'faculty' => 'Fakultas Demo',
            'study_program' => 'Program Studi Demo',
            'password' => 'password-kuat',
            'password_confirmation' => 'password-kuat',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_student_must_provide_study_program(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Mahasiswa Baru',
            'email' => 'student@example.test',
            'phone' => '0812-3456-7890',
            'nim_nip' => '20260002',
            'user_type' => 'student',
            'faculty' => 'Fakultas Demo',
            'password' => 'password-kuat',
            'password_confirmation' => 'password-kuat',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('study_program');
    }

    public function test_email_verification_routes_are_removed_and_no_notification_is_sent(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get('/email/verify')->assertNotFound();
        $this->actingAs($user)->post('/email/verification-notification')->assertNotFound();

        $this->assertNull($user->fresh()->email_verified_at);
        Notification::assertNothingSent();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.test',
            'password' => Hash::make('password-kuat'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password-kuat',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@example.test',
            'password' => Hash::make('password-kuat'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'login@example.test',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
