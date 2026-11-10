<?php

use App\Http\Controllers\Admin\SchoolAdminController;
use App\Http\Middleware\EnsureAdminDinas;
use Illuminate\Support\Facades\Route;

// Domain 1: Profil sekolah (PBI-01 / FR-01). Khusus Admin Dinas yang sudah login (sesi web).
Route::middleware(['auth', EnsureAdminDinas::class])
    ->prefix('admin/sekolah')
    ->name('admin.schools.')
    ->group(function () {
        Route::get('/', [SchoolAdminController::class, 'index'])->name('index');
        Route::post('/', [SchoolAdminController::class, 'store'])->name('store');
        Route::put('/{id}', [SchoolAdminController::class, 'update'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [SchoolAdminController::class, 'destroy'])->whereNumber('id')->name('destroy');
    });
