<?php

use App\Http\Controllers\DamageReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Domain 2: Pelaporan & Verifikasi (PBI-04)
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [DamageReportController::class, 'index'])->name('index');
    Route::get('/create', [DamageReportController::class, 'create'])->name('create');
    Route::post('/', [DamageReportController::class, 'store'])->name('store');
});
