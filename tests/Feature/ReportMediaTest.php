<?php

namespace Tests\Feature;

use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\ReportMedia;
use App\Models\School;
use Illuminate\Support\Facades\Schema;
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
}

