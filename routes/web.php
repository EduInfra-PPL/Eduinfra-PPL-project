<?php

    use App\Http\Controllers\AidCampaignController;
    use App\Http\Controllers\DamageReportController;
    use App\Http\Controllers\Renovasi\RenovasiController;
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

    // Domain 3: Manajemen Renovasi, RAB & Rekanan Vendor (Fakhira - PBI-07 & PBI-09)
    Route::prefix('renovasi')->name('renovasi.')->group(function () {
        Route::get('/', [RenovasiController::class, 'index'])->name('index');
        Route::post('/', [RenovasiController::class, 'store'])->name('store');

        Route::get('/rab', [RenovasiController::class, 'rab'])->name('rab');
        Route::get('/milestone', [RenovasiController::class, 'milestones'])->name('milestones');
        Route::post('/milestone/{id}', [RenovasiController::class, 'updateMilestone'])->name('milestones.update');

        Route::get('/vendors', [RenovasiController::class, 'vendors'])->name('vendors');

        Route::get('/{id}', [RenovasiController::class, 'showProject'])->name('projects.show');
        Route::delete('/{id}', [RenovasiController::class, 'destroy'])->name('projects.destroy');
        Route::get('/{id}/rab', [RenovasiController::class, 'showProjectRab'])->name('projects.rab');
    });

    // Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran
    Route::prefix('pendanaan')->name('pendanaan.')->group(function () {
        Route::get('/', [AidCampaignController::class, 'index'])->name('index');
        Route::post('/', [AidCampaignController::class, 'store'])->name('store');
    });

    // Domain 1: Profil sekolah (PBI-01)
    require __DIR__.'/schools.php';

    // Domain 5: Registrasi akun pengguna
    require __DIR__.'/registration.php';