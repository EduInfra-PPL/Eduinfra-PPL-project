<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DamageReportController;
use App\Http\Controllers\Renovasi\RenovasiController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Domain 2: Public Damage Reports (Dimas - PBI-04)
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [DamageReportController::class, 'index'])->name('index');
    Route::get('/create', [DamageReportController::class, 'create'])->name('create');
    Route::post('/', [DamageReportController::class, 'store'])->name('store');
});

// Domain 3: Manajemen Renovasi, RAB & Rekanan Vendor (Fakhira - PBI-07 & PBI-09)
Route::prefix('renovasi')->name('renovasi.')->group(function () {
    Route::get('/', [RenovasiController::class, 'index'])->name('index');
    Route::post('/', [RenovasiController::class, 'store'])->name('store'); // C (Create Proyek Baru)
    
    Route::get('/rab', [RenovasiController::class, 'rab'])->name('rab');
    Route::get('/milestone', [RenovasiController::class, 'milestones'])->name('milestones');
    Route::post('/milestone/{id}', [RenovasiController::class, 'updateMilestone'])->name('milestones.update'); // U (Update Milestone)
    
    Route::get('/vendors', [RenovasiController::class, 'vendors'])->name('vendors');
    
    Route::get('/{id}', [RenovasiController::class, 'showProject'])->name('projects.show'); // R (Detail Proyek)
    Route::delete('/{id}', [RenovasiController::class, 'destroy'])->name('projects.destroy'); // D (Delete Proyek)
    Route::get('/{id}/rab', [RenovasiController::class, 'showProjectRab'])->name('projects.rab');
});
