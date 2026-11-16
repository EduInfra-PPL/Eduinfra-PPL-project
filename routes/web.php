<?php

use App\Http\Controllers\Auth\RegistrationApprovalController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\UserDirectoryController;
use App\Http\Controllers\DamageReportController;
use App\Http\Middleware\EnsureAdminDinas;
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

// Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran
Route::prefix('pendanaan')->name('pendanaan.')->group(function () {
    Route::get('/', function () {
        return view('pendanaan.index');
    })->name('index');
});

// Domain 5: Registrasi akun pengguna (PBI-13)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('register.store');
});

// Persetujuan registrasi (khusus Admin Dinas)
Route::middleware(['auth', EnsureAdminDinas::class])
    ->prefix('admin/registrations')
    ->name('admin.registrations.')
    ->group(function () {
        Route::get('/', [RegistrationApprovalController::class, 'index'])->name('index');
        Route::post('/{user}/approve', [RegistrationApprovalController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [RegistrationApprovalController::class, 'reject'])->name('reject');
    });

// Daftar pengguna & aktivitas login (khusus Admin Dinas)
Route::middleware(['auth', EnsureAdminDinas::class])
    ->get('/admin/users', [UserDirectoryController::class, 'index'])
    ->name('admin.users.index');
