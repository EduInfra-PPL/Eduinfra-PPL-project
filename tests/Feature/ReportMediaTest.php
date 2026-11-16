<?php

namespace Tests\Feature;

use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\ReportMedia;
use App\Models\School;
use App\Services\Media\ExifExtractionService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportMediaTest extends TestCase
{
    /**
     * Memastikan tabel report_media memiliki seluruh kolom yang disyaratkan PBI-05.
     */
    public function test_report_media_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('report_media'));

        $expectedColumns = [
            'id',
            'report_id',
            'media_path',
            'file_name',
            'file_size',
            'mime_type',
            'exif_lat',
            'exif_lng',
            'captured_at',
            'distance_meters',
            'is_verified_location',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('report_media', $column),
                "Column [{$column}] does not exist on [report_media] table."
            );
        }
    }

    /**
     * Memastikan relasi Eloquent antara DamageReport dan ReportMedia berfungsi timbal balik.
     */
    public function test_report_media_and_damage_report_relationships(): void
    {
        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        // 1. Buat Laporan Kerusakan
        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-MEDIA-01',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Pengujian Relasi Media',
            'description' => 'Deskripsi pengujian relasi media PBI-05.',
            'severity_level' => 3,
            'status' => 'pending',
        ]);

        // 2. Buat Record ReportMedia
        $media = ReportMedia::create([
            'report_id' => $report->id,
            'media_path' => 'reports/media/sample_proof.jpg',
            'file_name' => 'sample_proof.jpg',
            'file_size' => 204800,
            'mime_type' => 'image/jpeg',
            'exif_lat' => 1.1892000,
            'exif_lng' => 109.3218000,
            'distance_meters' => 45,
            'is_verified_location' => true,
        ]);

        // 3. Verifikasi Relasi BelongsTo
        $this->assertInstanceOf(DamageReport::class, $media->damageReport);
        $this->assertEquals($report->id, $media->damageReport->id);

        // 4. Verifikasi Relasi HasMany
        $this->assertTrue($report->media->contains($media));
        $this->assertEquals(1, $report->media->count());

        // 5. Verifikasi Accessor & Scopes
        $this->assertStringContainsString('reports/media/sample_proof.jpg', $media->url);
        $this->assertEquals('Valid Geofence (45m)', $media->location_status_label);
        $this->assertEquals(1, ReportMedia::verifiedLocation()->where('id', $media->id)->count());

        // Cleanup
        $report->delete(); // Karena cascadeOnDelete, $media otomatis terhapus
        $this->assertDatabaseMissing('report_media', ['id' => $media->id]);
    }

    /**
     * Memastikan ekstensi PHP exif dan gd aktif untuk kebutuhan PBI-05.
     */
    public function test_php_exif_and_gd_extensions_are_loaded(): void
    {
        $this->assertTrue(extension_loaded('exif'), 'PHP Extension [exif] must be enabled.');
        $this->assertTrue(extension_loaded('gd'), 'PHP Extension [gd] must be enabled.');
    }

    /**
     * Uji validasi FormRequest saat mengunggah berkas media.
     */
    public function test_upload_media_validates_required_and_image_file(): void
    {
        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-VAL-01',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Pengujian Validasi Media',
            'description' => 'Deskripsi pengujian validasi upload media.',
            'severity_level' => 3,
            'status' => 'pending',
        ]);

        // 1. Tanpa berkas (File required)
        $responseEmpty = $this->postJson(route('reports.media.store', $report), []);
        $responseEmpty->assertStatus(422);
        $responseEmpty->assertJsonValidationErrors(['file']);

        // 2. Berkas bukan gambar (misal file text/pdf)
        $invalidFile = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');
        $responseInvalid = $this->postJson(route('reports.media.store', $report), ['file' => $invalidFile]);
        $responseInvalid->assertStatus(422);
        $responseInvalid->assertJsonValidationErrors(['file']);

        // Cleanup
        $report->delete();
    }

    /**
     * Uji alur upload foto tanpa GPS (disimpan dengan flag is_verified_location = false).
     */
    public function test_upload_media_without_gps_saves_as_unverified_flagged(): void
    {
        Storage::fake('public');

        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-NOGPS-01',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Pengujian Upload Tanpa GPS',
            'description' => 'Deskripsi pengujian upload foto tanpa tag EXIF GPS.',
            'severity_level' => 4,
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->image('bukti_kerusakan.jpg', 640, 480);

        $response = $this->postJson(route('reports.media.store', $report), ['file' => $file]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'file_name' => 'bukti_kerusakan.jpg',
                'is_verified_location' => false,
                'distance_meters' => null,
                'location_status' => 'Tanpa Metadata GPS',
            ],
        ]);

        $this->assertDatabaseHas('report_media', [
            'report_id' => $report->id,
            'file_name' => 'bukti_kerusakan.jpg',
            'is_verified_location' => false,
        ]);

        // Verifikasi fisik berkas tersimpan di storage publik
        $media = ReportMedia::where('report_id', $report->id)->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->media_path);

        // Cleanup
        $report->delete();
    }

    /**
     * Uji alur upload foto dengan koordinat GPS valid dalam radius geofence (<= 250 meter).
     */
    public function test_upload_media_with_gps_within_geofence_250m_is_verified(): void
    {
        Storage::fake('public');

        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();
        $coords = $school->getCoordinatesLatLng();
        $this->assertNotNull($coords, 'School coordinates must not be null');

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-GEOFENCE-OK',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Pengujian Upload Dalam Radius 250m',
            'description' => 'Deskripsi pengujian foto valid radius geofence.',
            'severity_level' => 5,
            'status' => 'pending',
        ]);

        // Mock ExifExtractionService mengembalikan titik foto berjarak ~30 meter dari sekolah
        $this->mock(ExifExtractionService::class, function ($mock) use ($coords) {
            $mock->shouldReceive('extract')->once()->andReturn([
                'has_gps' => true,
                'latitude' => $coords['latitude'] + 0.0002,
                'longitude' => $coords['longitude'] + 0.0002,
                'captured_at' => Carbon::parse('2026-11-09 10:15:00'),
                'raw_exif' => ['GPSLatitude' => 'dummy'],
            ]);
        });

        $file = UploadedFile::fake()->image('foto_valid_geofence.jpg', 800, 600);

        $response = $this->postJson(route('reports.media.store', $report), ['file' => $file]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'file_name' => 'foto_valid_geofence.jpg',
                'is_verified_location' => true,
            ],
        ]);

        $responseData = $response->json('data');
        $this->assertLessThanOrEqual(250, $responseData['distance_meters']);
        $this->assertStringContainsString('Valid Geofence', $responseData['location_status']);

        $this->assertDatabaseHas('report_media', [
            'report_id' => $report->id,
            'is_verified_location' => true,
        ]);

        // Cleanup
        $report->delete();
    }

    /**
     * Uji alur upload foto dengan koordinat GPS melebihi radius geofence (> 250 meter / Flagged).
     */
    public function test_upload_media_with_gps_exceeding_geofence_250m_is_flagged(): void
    {
        Storage::fake('public');

        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();
        $coords = $school->getCoordinatesLatLng();
        $this->assertNotNull($coords);

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-GEOFENCE-FLAG',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Pengujian Upload Di Luar Radius 250m',
            'description' => 'Deskripsi pengujian foto deviasi geofence.',
            'severity_level' => 4,
            'status' => 'pending',
        ]);

        // Mock ExifExtractionService mengembalikan titik foto berjarak ~1.5 kilometer dari sekolah
        $this->mock(ExifExtractionService::class, function ($mock) use ($coords) {
            $mock->shouldReceive('extract')->once()->andReturn([
                'has_gps' => true,
                'latitude' => $coords['latitude'] + 0.015,
                'longitude' => $coords['longitude'] + 0.015,
                'captured_at' => Carbon::parse('2026-11-09 11:30:00'),
                'raw_exif' => ['GPSLatitude' => 'dummy'],
            ]);
        });

        $file = UploadedFile::fake()->image('foto_flagged.jpg', 800, 600);

        $response = $this->postJson(route('reports.media.store', $report), ['file' => $file]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'file_name' => 'foto_flagged.jpg',
                'is_verified_location' => false,
            ],
        ]);

        $responseData = $response->json('data');
        $this->assertGreaterThan(250, $responseData['distance_meters']);
        $this->assertStringContainsString('Flagged: Deviasi', $responseData['location_status']);

        $this->assertDatabaseHas('report_media', [
            'report_id' => $report->id,
            'is_verified_location' => false,
        ]);

        // Cleanup
        $report->delete();
    }

    /**
     * Uji penghapusan berkas media via DELETE (menghapus berkas fisik storage dan record database).
     */
    public function test_delete_media_removes_physical_file_and_database_record(): void
    {
        Storage::fake('public');

        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-DEL-01',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Pengujian Hapus Media',
            'description' => 'Deskripsi pengujian hapus media.',
            'severity_level' => 2,
            'status' => 'pending',
        ]);

        $mediaPath = 'reports/media/dummy_to_delete.jpg';
        Storage::disk('public')->put($mediaPath, 'dummy content image');

        $media = ReportMedia::create([
            'report_id' => $report->id,
            'media_path' => $mediaPath,
            'file_name' => 'dummy_to_delete.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
            'is_verified_location' => false,
        ]);

        Storage::disk('public')->assertExists($mediaPath);
        $this->assertDatabaseHas('report_media', ['id' => $media->id]);

        // Eksekusi DELETE via Controller
        $response = $this->deleteJson(route('reports.media.destroy', $media));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi fisik dan DB terhapus bersih
        Storage::disk('public')->assertMissing($mediaPath);
        $this->assertDatabaseMissing('report_media', ['id' => $media->id]);

        // Cleanup
        $report->delete();
    }

    /**
     * Uji respon non-JSON (HTML form submission) diarahkan kembali dengan session flash.
     */
    public function test_upload_media_standard_http_redirects_back_with_flash_message(): void
    {
        Storage::fake('public');

        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-REDIRECT-01',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Pengujian Form Redirect Media',
            'description' => 'Deskripsi pengujian standard redirect.',
            'severity_level' => 2,
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->image('bukti_biasa.jpg', 400, 300);

        $response = $this->from(route('reports.create'))
            ->post(route('reports.media.store', $report), ['file' => $file]);

        $response->assertRedirect(route('reports.create'));
        $response->assertSessionHas('success');

        // Cleanup
        $report->delete();
    }
}
