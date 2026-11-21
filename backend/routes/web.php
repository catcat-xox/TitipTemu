<?php

use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminReturnConfirmationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LostReportController;
use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\ReturnConfirmationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::get('/barang/{type}', [PublicReportController::class, 'index'])
    ->whereIn('type', ['lost', 'found'])
    ->name('reports.index');
Route::get('/barang/{type}/{report}', [PublicReportController::class, 'show'])
    ->whereIn('type', ['lost', 'found'])
    ->name('reports.show');
Route::view('/panduan', 'pages.guide')->name('guide');
Route::get('/laporan/{report}/foto', [PublicReportController::class, 'photo'])
    ->name('reports.photo');

Route::middleware('auth')->group(function () {
    Route::get('/lapor-kehilangan', [LostReportController::class, 'create'])
        ->name('reports.create');
    Route::post('/lapor-kehilangan', [LostReportController::class, 'store'])
        ->name('reports.store');
    Route::get('/laporan-saya', [LostReportController::class, 'mine'])
        ->name('reports.mine');
    Route::get('/laporan-saya/{report}/edit', [LostReportController::class, 'edit'])
        ->name('reports.edit');
    Route::put('/laporan-saya/{report}', [LostReportController::class, 'update'])
        ->name('reports.update');
    Route::post('/laporan-saya/{report}/batalkan', [LostReportController::class, 'cancel'])
        ->name('reports.cancel');
    Route::post('/laporan-saya/{report}/kirim-ulang', [LostReportController::class, 'resubmit'])
        ->name('reports.resubmit');
    Route::get('/laporan-saya/{report}/pengembalian', [ReturnConfirmationController::class, 'create'])
        ->name('reports.return.create');
    Route::post('/laporan-saya/{report}/pengembalian', [ReturnConfirmationController::class, 'store'])
        ->name('reports.return.store');
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
        Route::get('/dashboard', [AdminReportController::class, 'index'])->name('dashboard');
        Route::get('/laporan/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::get('/laporan/{report}/foto', [AdminReportController::class, 'photo'])->name('reports.photo');
        Route::post('/laporan/{report}/moderasi', [AdminReportController::class, 'moderate'])->name('reports.moderate');
        Route::post('/laporan/{report}/selesaikan-pengembalian', [AdminReturnConfirmationController::class, 'resolve'])->name('reports.resolve-return');
        Route::get('/pengembalian/{confirmation}/bukti', [AdminReturnConfirmationController::class, 'proof'])->name('returns.proof');
        Route::post('/pengembalian/{confirmation}/setujui', [AdminReturnConfirmationController::class, 'approve'])->name('returns.approve');
        Route::post('/pengembalian/{confirmation}/tolak', [AdminReturnConfirmationController::class, 'reject'])->name('returns.reject');
    });
