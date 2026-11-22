<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminReturnConfirmationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LostReportController;
use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\ReturnConfirmationController;
use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;

/*
 * Situs utama (Guest dan User) memakai Public/User Layout.
 * Middleware main-site menjaga Admin tetap berada di panel Admin.
 */
Route::middleware('main-site')->group(function (): void {
    Route::get('/', [PublicReportController::class, 'home'])->name('home');
    Route::get('/barang/{type}', [PublicReportController::class, 'index'])
        ->whereIn('type', ['lost', 'found'])
        ->name('reports.index');
    Route::get('/barang/{type}/{report}', [PublicReportController::class, 'show'])
        ->whereIn('type', ['lost', 'found'])
        ->name('reports.show');
    Route::view('/panduan', 'pages.guide')->name('guide');
    Route::get('/laporan/{report}/foto', [PublicReportController::class, 'photo'])
        ->name('reports.photo');
    Route::get('/laporan/{report}/hubungi', [PublicReportController::class, 'contact'])
        ->name('reports.contact');

    Route::middleware('role:guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    });
});

/*
 * Area User. Admin tidak boleh melihat UI ini; role:user memberi 403.
 */
Route::middleware('role:user')->group(function (): void {
    Route::get('/laporkan-barang-temuan', [LostReportController::class, 'create'])
        ->name('reports.create');
    Route::post('/laporkan-barang-temuan', [LostReportController::class, 'store'])
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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/akun', [UserAccountController::class, 'index'])->name('user.dashboard');
    Route::put('/akun/profil', [UserAccountController::class, 'update'])->name('user.profile.update');
    Route::get('/akun/foto-profil', [UserAccountController::class, 'photo'])->name('user.profile-photo');
    Route::redirect('/user/dashboard', '/akun')->name('user.dashboard.legacy');
});

/*
 * Panel Admin memakai layout dan guard terpisah. Jalur login tidak ditautkan
 * dari situs utama dan berasal dari konfigurasi ADMIN_LOGIN_URI.
 */
$adminLoginUri = '/'.ltrim((string) config('admin.login_uri'), '/');

Route::middleware('role:guest')->group(function () use ($adminLoginUri): void {
    Route::get($adminLoginUri, [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post($adminLoginUri, [AdminAuthController::class, 'login'])->name('admin.login.store');
});

Route::middleware('role:admin')->group(function (): void {
    Route::post('/admin/keluar', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

Route::middleware('role:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [AdminReportController::class, 'index'])->name('dashboard');
        Route::get('/cari-laporan', [AdminReportController::class, 'search'])->name('reports.search');
        Route::get('/laporan/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::get('/laporan/{report}/edit', [AdminReportController::class, 'edit'])->name('reports.edit');
        Route::put('/laporan/{report}', [AdminReportController::class, 'update'])->name('reports.update');
        Route::delete('/laporan/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('/laporan/{report}/foto', [AdminReportController::class, 'photo'])->name('reports.photo');
        Route::post('/laporan/{report}/verifikasi', [AdminReportController::class, 'moderate'])->name('reports.verify');
        Route::post('/laporan/{report}/selesaikan-pengembalian', [AdminReturnConfirmationController::class, 'resolve'])->name('reports.resolve-return');
        Route::get('/pengembalian/{confirmation}/bukti', [AdminReturnConfirmationController::class, 'proof'])->name('returns.proof');
        Route::post('/pengembalian/{confirmation}/setujui', [AdminReturnConfirmationController::class, 'approve'])->name('returns.approve');
        Route::post('/pengembalian/{confirmation}/tolak', [AdminReturnConfirmationController::class, 'reject'])->name('returns.reject');
    });
