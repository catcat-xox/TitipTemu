<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.admin-login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email atau password Admin tidak sesuai.']);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Anda berhasil masuk sebagai Admin.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('status', 'Anda telah keluar dari akun Admin.');
    }
}
