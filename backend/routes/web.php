<?php

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
