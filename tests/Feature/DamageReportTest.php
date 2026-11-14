<?php

namespace Tests\Feature;

use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\ReportMedia;
use App\Models\School;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DamageReportTest extends TestCase
{
    /**
     * Uji apakah halaman daftar laporan (/reports) dapat diakses dengan sukses.
     */
    public function test_reports_index_page_can_be_rendered(): void
    {
        $response = $this->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Daftar Laporan Fasilitas Sekolah');
        $response->assertSee('Total Tiket Masuk');
        $response->assertSee('Buat Laporan Kerusakan Baru');
    }

    /**
     * Uji apakah halaman formulir pelaporan (/reports/create) dapat diakses dengan sukses.
     */
    public function test_reports_create_page_can_be_rendered(): void
    {
        $response = $this->get(route('reports.create'));

        $response->assertOk();
        $response->assertSee('Laporan Kerusakan Fasilitas Sekolah');
        $response->assertSee('Identifikasi Satuan Pendidikan');
        $response->assertSee('Titik Master GPS Sekolah');
        $response->assertSee('Protokol EXIF v4.2');
    }

    /**
     * Uji validasi penyimpanan laporan baru (PBI-04).
     */
    public function test_reports_store_validates_required_fields(): void
    {
        $response = $this->post(route('reports.store'), []);

        $response->assertSessionHasErrors([
            'school_id',
            'category_id',
            'title',
            'description',
            'severity_level',
        ]);
    }

    /**
     * Uji penyimpanan laporan baru berhasil dan redirect ke index dengan notifikasi flash (PBI-04).
     */
    public function test_reports_store_creates_record_and_redirects(): void
    {
        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        $payload = [
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Kerusakan RKB Akibat Angin Kencang',
            'description' => 'Bagian atap dan kusen jendela ruang kelas 3 terlepas karena terpaan angin badai.',
            'severity_level' => 5,
        ];

        $response = $this->post(route('reports.store'), $payload);

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('damage_reports', [
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Kerusakan RKB Akibat Angin Kencang',
            'severity_level' => 5,
            'status' => 'pending',
        ]);

        // Cleanup
        DamageReport::where('title', 'Kerusakan RKB Akibat Angin Kencang')->delete();
    }

    /**
     * Uji Model DamageReport: Accessor label tingkat keparahan.
     */
    public function test_damage_report_severity_label_accessor(): void
    {
        $report = new DamageReport(['severity_level' => 5]);
        $this->assertEquals('Level 5 – Kritis', $report->severity_label);

        $report->severity_level = 3;
        $this->assertEquals('Level 3 – Sedang', $report->severity_label);

        $report->severity_level = 1;
        $this->assertEquals('Level 1 – Ringan', $report->severity_label);
    }

    /**
     * Uji Model DamageReport: Accessor label status tiket.
     */
    public function test_damage_report_status_label_accessor(): void
    {
        $report = new DamageReport(['status' => 'pending']);
        $this->assertEquals('Pending Visitasi Lapangan', $report->status_label);

        $report->status = 'verified';
        $this->assertEquals('Verified Auditor', $report->status_label);

        $report->status = 'in_repair';
        $this->assertEquals('Dalam Renovasi', $report->status_label);
    }

    /**
     * Uji definisi relasi Eloquent pada Model DamageReport, School, dan FacilityCategory.
     */
    public function test_damage_report_model_relations_defined(): void
    {
        $report = new DamageReport();

        $this->assertTrue(method_exists($report, 'school'));
        $this->assertTrue(method_exists($report, 'category'));
        $this->assertTrue(method_exists($report, 'reporter'));
        $this->assertTrue(method_exists($report, 'media'));

        $school = new School();
        $this->assertTrue(method_exists($school, 'damageReports'));

        $category = new FacilityCategory();
        $this->assertTrue(method_exists($category, 'damageReports'));
    }

    /**
     * Uji validasi berkas foto pada request pembuatan laporan (PBI-05).
     */
    public function test_reports_store_validates_photos_array_and_mimes(): void
    {
        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        // 1. Uji berkas bukan gambar (misal file text/pdf)
        $invalidFile = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');
        $payloadInvalidMime = [
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Laporan Uji Format Salah',
            'description' => 'Deskripsi laporan uji format file bukan gambar.',
            'severity_level' => 3,
            'photos' => [$invalidFile],
        ];

        $responseInvalid = $this->post(route('reports.store'), $payloadInvalidMime);
        $responseInvalid->assertSessionHasErrors(['photos.0']);

        // 2. Uji batas maksimal 5 berkas foto
        $tooManyPhotos = [
            UploadedFile::fake()->image('foto1.jpg'),
            UploadedFile::fake()->image('foto2.jpg'),
            UploadedFile::fake()->image('foto3.jpg'),
            UploadedFile::fake()->image('foto4.jpg'),
            UploadedFile::fake()->image('foto5.jpg'),
            UploadedFile::fake()->image('foto6.jpg'),
        ];
        $payloadTooMany = [
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Laporan Uji Lebih Dari 5 Foto',
            'description' => 'Deskripsi laporan uji kuota unggahan melebihi batas 5 foto.',
            'severity_level' => 3,
            'photos' => $tooManyPhotos,
        ];

        $responseTooMany = $this->post(route('reports.store'), $payloadTooMany);
        $responseTooMany->assertSessionHasErrors(['photos']);
    }

    /**
     * Uji alur E2E pembuatan laporan baru beserta lampiran berkas foto bukti fisik (PBI-05).
     */
    public function test_reports_store_with_photos_creates_report_and_media_records_e2e(): void
    {
        Storage::fake('public');

        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();

        $photo1 = UploadedFile::fake()->image('atap_rusak.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('plafon_ambruk.png', 800, 600);

        $payload = [
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Kerusakan Berat Ruang Kelas E2E PBI-05',
            'description' => 'Atap dan plafon ruang kelas 2 ambruk membutuhkan renovasi darurat.',
            'severity_level' => 5,
            'photos' => [$photo1, $photo2],
        ];

        $response = $this->post(route('reports.store'), $payload);

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success');

        $report = DamageReport::where('title', 'Kerusakan Berat Ruang Kelas E2E PBI-05')->first();
        $this->assertNotNull($report, 'Record laporan kerusakan harus berhasil dibuat.');
        $this->assertStringStartsWith('RPT-', $report->ticket_num);

        // Verifikasi relasi media terbuat dan terhubung
        $this->assertEquals(2, $report->media()->count());

        $mediaItems = $report->media()->get();
        foreach ($mediaItems as $media) {
            $this->assertEquals($report->id, $media->report_id);
            $this->assertNotEmpty($media->media_path);
            $this->assertTrue(Storage::disk('public')->exists($media->media_path));
        }

        // Cleanup
        $report->media()->delete();
        $report->delete();
    }
}

