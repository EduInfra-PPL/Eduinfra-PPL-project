<?php

namespace Tests\Unit;

use App\Services\Media\ExifExtractionService;
use App\Services\Media\ImagePrivacyStripperService;
use App\Services\Spatial\SpatialDistanceService;
use Carbon\Carbon;
use Tests\TestCase;

class ExifAndGeofenceServiceTest extends TestCase
{
    protected ExifExtractionService $exifService;
    protected SpatialDistanceService $spatialService;
    protected ImagePrivacyStripperService $stripperService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exifService = new ExifExtractionService();
        $this->spatialService = new SpatialDistanceService();
        $this->stripperService = new ImagePrivacyStripperService();
    }

    /**
     * 1. Uji konversi koordinat GPS format DMS ke Desimal WGS84 untuk Belahan Bumi Utara & Timur (RI).
     */
    public function test_converts_dms_to_decimal_north_and_east(): void
    {
        // Contoh: 1° 11' 21.12" N (Wilayah Temajuk, Sambas, Kalbar)
        $dmsLat = ['1/1', '11/1', '2112/100'];
        $decimalLat = $this->exifService->convertDmsToDecimal($dmsLat, 'N');
        $this->assertNotNull($decimalLat);
        $this->assertEqualsWithDelta(1.1892, $decimalLat, 0.0001);

        // Contoh: 109° 19' 18.48" E
        $dmsLng = ['109/1', '19/1', '1848/100'];
        $decimalLng = $this->exifService->convertDmsToDecimal($dmsLng, 'E');
        $this->assertNotNull($decimalLng);
        $this->assertEqualsWithDelta(109.3218, $decimalLng, 0.0001);
    }

    /**
     * 2. Uji konversi koordinat GPS format DMS ke Desimal untuk Belahan Bumi Selatan & Barat (Nilai Negatif).
     */
    public function test_converts_dms_to_decimal_south_and_west(): void
    {
        // Contoh: 6° 12' 00" S (Jakarta / Jawa) -> harus bernilai minus (-6.2)
        $dmsLat = ['6/1', '12/1', '0/1'];
        $decimalLat = $this->exifService->convertDmsToDecimal($dmsLat, 'S');
        $this->assertNotNull($decimalLat);
        $this->assertEqualsWithDelta(-6.2, $decimalLat, 0.0001);

        // Contoh: Barat (W) -> harus bernilai minus
        $dmsLng = ['75/1', '30/1', '0/1'];
        $decimalLng = $this->exifService->convertDmsToDecimal($dmsLng, 'W');
        $this->assertNotNull($decimalLng);
        $this->assertEqualsWithDelta(-75.5, $decimalLng, 0.0001);
    }

    /**
     * 3. Uji fallback ExifExtractionService jika file tidak ada atau bukan gambar valid.
     */
    public function test_exif_extractor_returns_default_when_file_not_found(): void
    {
        $result = $this->exifService->extract('non_existent_file.jpg');

        $this->assertFalse($result['has_gps']);
        $this->assertNull($result['latitude']);
        $this->assertNull($result['longitude']);
        $this->assertNull($result['captured_at']);
        $this->assertEmpty($result['raw_exif']);
    }

    /**
     * 4. Uji kalkulasi jarak Haversine (titik yang sama menghasilkan jarak 0 meter).
     */
    public function test_haversine_identical_points_return_zero_meters(): void
    {
        $lat = 1.1892;
        $lng = 109.3218;

        $distance = $this->spatialService->calculateDistance($lat, $lng, $lat, $lng);
        $this->assertEquals(0, $distance);
        $this->assertTrue($this->spatialService->isWithinGeofence($distance));
    }

    /**
     * 5. Uji kalkulasi jarak deviasi dalam radius toleransi geofence (<= 250 meter).
     */
    public function test_haversine_points_within_geofence_tolerance_250m(): void
    {
        // Titik 1: Master Sekolah (1.1892, 109.3218)
        // Titik 2: Foto berjarak ~45 meter (offset koordinat kecil)
        $schoolLat = 1.189200;
        $schoolLng = 109.321800;

        $photoLat = 1.189500;
        $photoLng = 109.322000;

        $distance = $this->spatialService->calculateDistance($photoLat, $photoLng, $schoolLat, $schoolLng);

        // Verifikasi jarak ~40-60 meter (pasti <= 250m)
        $this->assertGreaterThan(0, $distance);
        $this->assertLessThanOrEqual(250, $distance);
        $this->assertTrue($this->spatialService->isWithinGeofence($distance));
    }

    /**
     * 6. Uji kalkulasi deviasi melebihi batas radius geofence (> 250 meter / Flagged).
     */
    public function test_haversine_points_exceeding_geofence_tolerance_flagged(): void
    {
        // Titik 1: Master Sekolah (1.1892, 109.3218)
        // Titik 2: Foto diambil di lokasi berjarak ~1.5 kilometer
        $schoolLat = 1.189200;
        $schoolLng = 109.321800;

        $photoLat = 1.199200;
        $photoLng = 109.331800;

        $distance = $this->spatialService->calculateDistance($photoLat, $photoLng, $schoolLat, $schoolLng);

        // Verifikasi jarak > 250m dan geofence bernilai FALSE
        $this->assertGreaterThan(250, $distance);
        $this->assertFalse($this->spatialService->isWithinGeofence($distance));
    }

    /**
     * 7. Uji validasi isWithinGeofence dengan input null.
     */
    public function test_geofence_returns_false_for_null_distance(): void
    {
        $this->assertFalse($this->spatialService->isWithinGeofence(null));
    }

    /**
     * 8. Uji ImagePrivacyStripperService menghasilkan berkas bersih tanpa error.
     */
    public function test_privacy_stripper_processes_image_cleanly(): void
    {
        // Buat file gambar dummy menggunakan GD
        $dummyImage = imagecreatetruecolor(100, 100);
        $bgColor = imagecolorallocate($dummyImage, 255, 92, 42);
        imagefill($dummyImage, 0, 0, $bgColor);

        $tempDir = sys_get_temp_dir();
        $sourcePath = $tempDir . DIRECTORY_SEPARATOR . 'test_source_' . uniqid() . '.jpg';
        $destPath = $tempDir . DIRECTORY_SEPARATOR . 'test_stripped_' . uniqid() . '.jpg';

        imagejpeg($dummyImage, $sourcePath, 90);
        imagedestroy($dummyImage);

        $this->assertFileExists($sourcePath);

        // Jalankan service pembersih privasi
        $success = $this->stripperService->stripAndSave($sourcePath, $destPath);

        $this->assertTrue($success);
        $this->assertFileExists($destPath);
        $this->assertGreaterThan(0, filesize($destPath));

        // Bersihkan file sementara
        @unlink($sourcePath);
        @unlink($destPath);
    }
}

