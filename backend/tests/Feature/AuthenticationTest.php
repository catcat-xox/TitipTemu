<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
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
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'User Baru',
            'email' => 'user.baru@example.test',
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
            'nim_nip' => '20260001',
            'user_type' => 'student',
            'faculty' => 'Fakultas Demo',
            'study_program' => 'Program Studi Demo',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'user.baru@example.test',
            'email_verified_at' => null,
        ]);
        Notification::assertSentTo($this->app['auth']->user(), VerifyEmail::class);
    }

    public function test_registration_rejects_invalid_input(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'nim_nip' => '',
            'user_type' => 'student',
            'faculty' => '',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_student_must_provide_study_program(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Mahasiswa Baru',
            'email' => 'student@example.test',
            'nim_nip' => '20260002',
            'user_type' => 'student',
            'faculty' => 'Fakultas Demo',
            'password' => 'password-kuat',
            'password_confirmation' => 'password-kuat',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('study_program');
    }

    public function test_email_can_be_verified_with_signed_url(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())],
        );

        $this->actingAs($user)->get($verificationUrl)
            ->assertRedirect(route('home'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_user_can_request_another_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post('/email/verification-notification')
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmail::class);
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
