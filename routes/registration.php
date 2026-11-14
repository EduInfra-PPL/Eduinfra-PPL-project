<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationApprovalController;
use App\Http\Middleware\EnsureAdminDinas;
use App\Http\Controllers\Auth\UserDirectoryController;

// Domain 5: Registrasi akun pengguna (di-require dari routes/web.php).
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