<?php

namespace App\Actions\Reports;

use App\Models\DamageReport;
use App\Models\ReportMedia;
use App\Services\Media\ExifExtractionService;
use App\Services\Media\ImagePrivacyStripperService;
use App\Services\Spatial\SpatialDistanceService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Action Class untuk pemrosesan berkas media bukti fisik kerusakan.
 * Membungkus alur: Ekstraksi EXIF -> Kalkulasi Geofence 250m -> Privacy Stripper -> ACID Transaction.
 * Mematuhi prinsip Single Responsibility & Strict Thin Controller (AGENT_RULE.md).
 */
class ProcessReportMediaAction
{
    public function __construct(
        protected ExifExtractionService $exifService,
        protected SpatialDistanceService $spatialService,
        protected ImagePrivacyStripperService $stripperService,
    ) {}

    /**
     * Eksekusi pemrosesan dan penyimpanan media bukti kerusakan.
     *
     * @param DamageReport $report Laporan kerusakan target
     * @param UploadedFile $file Berkas foto yang diunggah
     * @return ReportMedia Record media yang tersimpan
     */
    public function execute(DamageReport $report, UploadedFile $file): ReportMedia
    {
        $realPath = $file->getRealPath();
        $originalFileName = $file->getClientOriginalName();
        $fileSize = $file->getSize() ?: 0;
        $mimeType = $file->getMimeType() ?: 'image/jpeg';
        $extension = $file->getClientOriginalExtension() ?: 'jpg';

        // 1. Ekstraksi Metadata EXIF GPS & Tanggal Pemotretan
        $exifData = $this->exifService->extract($realPath);

        // 2. Kalkulasi Jarak Spasial terhadap Koordinat Master Sekolah
        $distanceMeters = null;
        $isVerifiedLocation = false;

        if ($exifData['has_gps'] && $exifData['latitude'] !== null && $exifData['longitude'] !== null) {
            $school = $report->school;
            $schoolCoords = $school?->getCoordinatesLatLng();

            if ($schoolCoords) {
                $distanceMeters = $this->spatialService->calculateDistance(
                    $exifData['latitude'],
                    $exifData['longitude'],
                    $schoolCoords['latitude'],
                    $schoolCoords['longitude']
                );

                $isVerifiedLocation = $this->spatialService->isWithinGeofence($distanceMeters);
            }
        }

        // 3. Nama Berkas Unik & Path Penyimpanan Publik
        $randomFileName = Str::random(40) . '.' . strtolower($extension);
        $relativeDirectory = 'reports/media';
        $relativePath = $relativeDirectory . '/' . $randomFileName;
        $absoluteDestination = Storage::disk('public')->path($relativePath);

        // 4. Privacy Stripping: Bersihkan metadata serial number kamera & IMEI sebelum disimpan
        $stripped = $this->stripperService->stripAndSave($realPath, $absoluteDestination);

        // Fallback jika stripper gagal (misal WebP tertentu), simpan file standar
        if (!$stripped || !file_exists($absoluteDestination)) {
            $file->storeAs($relativeDirectory, $randomFileName, 'public');
        }

        // 5. Simpan Record ke Database dalam DB::transaction
        return DB::transaction(function () use (
            $report,
            $relativePath,
            $originalFileName,
            $fileSize,
            $mimeType,
            $exifData,
            $distanceMeters,
            $isVerifiedLocation
        ) {
            return ReportMedia::create([
                'report_id' => $report->id,
                'media_path' => $relativePath,
                'file_name' => $originalFileName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'exif_lat' => $exifData['latitude'],
                'exif_lng' => $exifData['longitude'],
                'captured_at' => $exifData['captured_at'],
                'distance_meters' => $distanceMeters,
                'is_verified_location' => $isVerifiedLocation,
            ]);
        });
    }
}
