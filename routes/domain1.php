<?php

use App\Http\Controllers\Api\V1\FacilityCategoryController;
use App\Http\Controllers\Api\V1\RegionController;
use App\Http\Controllers\Api\V1\SchoolController;
use Illuminate\Support\Facades\Route;

/*
| Domain 1 - Master Wilayah dan Kelembagaan Pendidikan (PBI-01, 02, 03)
| Dimuat dari routes/api.php:  require __DIR__.'/domain1.php';
| (routes/api.php otomatis berprefiks /api, sehingga URL akhir = /api/v1/...)
*/
Route::prefix('v1')->group(function () {

    // ---- Baca: publik (Tabel 15) ----
    Route::get('schools', [SchoolController::class, 'index']);
    Route::get('schools/nearby', [SchoolController::class, 'nearby']);
    Route::get('schools/geojson', [SchoolController::class, 'geojson']);
    Route::get('schools/{id}', [SchoolController::class, 'show'])->whereNumber('id');

    Route::get('regions', [RegionController::class, 'index']);
    Route::get('regions/geojson', [RegionController::class, 'geojson']);
    Route::get('regions/lookup', [RegionController::class, 'lookup']);
    Route::get('regions/{id}', [RegionController::class, 'show'])->whereNumber('id');

    Route::get('facility-categories', [FacilityCategoryController::class, 'index']);

    // ---- Tulis: Admin Dinas ----
    Route::middleware(config('eduinfra.write_middleware'))->group(function () {
        Route::post('schools', [SchoolController::class, 'store']);
        Route::post('schools/import', [SchoolController::class, 'import']);
        Route::put('schools/{id}', [SchoolController::class, 'update'])->whereNumber('id');
        Route::delete('schools/{id}', [SchoolController::class, 'destroy'])->whereNumber('id');

        Route::post('regions/import', [RegionController::class, 'import']);
        Route::patch('regions/{id}/tier', [RegionController::class, 'updateTier'])->whereNumber('id');
        Route::delete('regions/{id}', [RegionController::class, 'destroy'])->whereNumber('id');

        Route::post('facility-categories', [FacilityCategoryController::class, 'store']);
        Route::patch('facility-categories/{id}', [FacilityCategoryController::class, 'update'])->whereNumber('id');
        Route::delete('facility-categories/{id}', [FacilityCategoryController::class, 'destroy'])->whereNumber('id');
    });
});
