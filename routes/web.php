<?php

use App\Http\Controllers\Admin\FacilityCategoryAdminController;
use App\Http\Controllers\Admin\SchoolAdminController;
use App\Http\Controllers\AidCampaignController;
use App\Http\Controllers\AidContributionController;
use App\Http\Controllers\Auth\RegistrationApprovalController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\UserDirectoryController;
use App\Http\Controllers\DamageReportController;
use App\Http\Controllers\Renovasi\RenovasiController;
use App\Http\Controllers\ReportMediaController;
use App\Http\Middleware\EnsureAdminDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Gerbang Awal: Halaman Loading Pemeriksaan Sesi (mengarahkan ke Login atau Dashboard)
Route::view('/', 'auth.session-check')->name('home');

// Endpoint pembersihan sesi ketika tab/URL ditutup atau dibuka di tab baru
Route::post('/session/expire-tab', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['expired' => true]);
})->name('session.expire-tab');

// Seluruh halaman aplikasi inti wajib login (middleware auth)
Route::middleware('auth')->group(function () {
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

    // Domain 3: Manajemen Renovasi, RAB & Rekanan Vendor (Fakhira - PBI-07 & PBI-09)
    Route::prefix('renovasi')->name('renovasi.')->group(function () {
        Route::get('/', [RenovasiController::class, 'index'])->name('index');
        Route::post('/', [RenovasiController::class, 'store'])->name('store');

        Route::get('/rab', [RenovasiController::class, 'rab'])->name('rab');
        Route::get('/milestone', [RenovasiController::class, 'milestones'])->name('milestones');
        Route::post('/milestone/{id}', [RenovasiController::class, 'updateMilestone'])->name('milestones.update');

        Route::get('/vendors', [RenovasiController::class, 'vendors'])->name('vendors');
        Route::post('/vendors', [RenovasiController::class, 'storeVendor'])->name('vendors.store');
        Route::put('/vendors/{vendor}', [RenovasiController::class, 'updateVendor'])->name('vendors.update');
        Route::patch('/vendors/{vendor}/blacklist', [RenovasiController::class, 'toggleBlacklistVendor'])->name('vendors.blacklist');
        Route::delete('/vendors/{vendor}', [RenovasiController::class, 'destroyVendor'])->name('vendors.destroy');

        Route::get('/{id}', [RenovasiController::class, 'showProject'])->name('projects.show');
        Route::delete('/{id}', [RenovasiController::class, 'destroy'])->name('projects.destroy');
        Route::get('/{id}/rab', [RenovasiController::class, 'showProjectRab'])->name('projects.rab');
    });

    // Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran
    Route::prefix('pendanaan')->name('pendanaan.')->group(function () {
        Route::get('/', [AidCampaignController::class, 'index'])->name('index');
        Route::post('/', [AidCampaignController::class, 'store'])->name('store');
        Route::get('/wall-of-donors', [AidContributionController::class, 'wallOfDonors'])->name('wall-of-donors');
        Route::post('/kontribusi', [AidContributionController::class, 'store'])->name('contributions.store');
    });

    // Domain 1: Master data, khusus Admin Dinas
    Route::middleware(EnsureAdminDinas::class)->prefix('admin')->name('admin.')->group(function () {
        // Profil sekolah (PBI-01 / FR-01)
        Route::prefix('sekolah')->name('schools.')->group(function () {
            Route::get('/', [SchoolAdminController::class, 'index'])->name('index');
            Route::post('/', [SchoolAdminController::class, 'store'])->name('store');
            Route::put('/{id}', [SchoolAdminController::class, 'update'])->whereNumber('id')->name('update');
            Route::delete('/{id}', [SchoolAdminController::class, 'destroy'])->whereNumber('id')->name('destroy');
        });

        // Kategori fasilitas dan bobot urgensi (PBI-03 / FR-03)
        Route::prefix('kategori-fasilitas')->name('categories.')->group(function () {
            Route::get('/', [FacilityCategoryAdminController::class, 'index'])->name('index');
            Route::post('/', [FacilityCategoryAdminController::class, 'store'])->name('store');
            Route::patch('/{id}', [FacilityCategoryAdminController::class, 'update'])->whereNumber('id')->name('update');
            Route::patch('/{id}/aktifkan', [FacilityCategoryAdminController::class, 'activate'])->whereNumber('id')->name('activate');
            Route::delete('/{id}', [FacilityCategoryAdminController::class, 'destroy'])->whereNumber('id')->name('destroy');
        });
    });
});

// Domain 5: Registrasi akun pengguna, Approval, & Matriks RBAC (PBI-13 & PBI-14)
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

// Tata Kelola Pengguna & Matriks RBAC (khusus Admin Dinas - PBI-14)
Route::middleware(['auth', EnsureAdminDinas::class])
    ->prefix('admin/users')
    ->name('admin.users.')
    ->group(function () {
        Route::get('/', [UserDirectoryController::class, 'index'])->name('index');
        Route::get('/export-audit-log', [UserDirectoryController::class, 'exportAuditLog'])->name('export');
        Route::patch('/{user}/role', [UserDirectoryController::class, 'updateRole'])->name('update-role');
        Route::post('/custom-roles', [UserDirectoryController::class, 'storeCustomRole'])->name('custom-roles.store');
    });
