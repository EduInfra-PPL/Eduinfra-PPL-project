<?php

use App\Http\Controllers\DamageReportController;
use App\Http\Controllers\ReportMediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Domain 2: Pelaporan & Verifikasi (PBI-04 & PBI-05)
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [DamageReportController::class, 'index'])->name('index');
    Route::get('/create', [DamageReportController::class, 'create'])->name('create');
    Route::post('/', [DamageReportController::class, 'store'])->name('store');

    // PBI-05: Unggahan Media & Validasi EXIF GPS
    Route::post('/{report}/media', [ReportMediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{media}', [ReportMediaController::class, 'destroy'])->name('media.destroy');
});
