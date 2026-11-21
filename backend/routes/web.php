<?php

use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::get('/barang/{type}', [\App\Http\Controllers\PublicReportController::class, 'index'])
    ->whereIn('type', ['lost', 'found'])
    ->name('reports.index');
Route::get('/barang/{type}/{report}', [\App\Http\Controllers\PublicReportController::class, 'show'])
    ->whereIn('type', ['lost', 'found'])
    ->name('reports.show');
Route::view('/panduan', 'pages.guide')->name('guide');

Route::middleware('auth')->group(function () {
    Route::get('/lapor-kehilangan', [\App\Http\Controllers\LostReportController::class, 'create'])
        ->name('reports.create');
    Route::post('/lapor-kehilangan', [\App\Http\Controllers\LostReportController::class, 'store'])
        ->name('reports.store');
Route::get('/laporan-saya', [\App\Http\Controllers\LostReportController::class, 'mine'])
        ->name('reports.mine');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request): RedirectResponse {
        $request->fulfill();

        return redirect()->route('home')
            ->with('success', 'Email berhasil diverifikasi.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function (): void {
        Route::view('/dashboard', 'pages.user.dashboard')->name('dashboard');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::view('/dashboard', 'pages.admin.dashboard')->name('dashboard');
    });
