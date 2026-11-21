<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email atau password tidak sesuai.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Anda berhasil masuk.');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->trim()->toString(),
            'email' => $request->string('email')->lower()->toString(),
            'nim_nip' => $request->string('nim_nip')->trim()->toString(),
            'user_type' => $request->string('user_type')->toString(),
            'faculty' => $request->string('faculty')->trim()->toString(),
            'study_program' => $request->string('study_program')->trim()->toString() ?: null,
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Akun berhasil dibuat. Selamat datang di TitipTemu.');
    }

    public function verificationNotice(): View
    {
        return view('auth.verify-email');
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Tautan verifikasi telah dikirim ulang.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('status', 'Anda telah keluar dari akun.');
    }
}
