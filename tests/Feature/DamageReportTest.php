<?php

namespace Tests\Feature;

use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\School;
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
        $response->assertSee('SDN 02 Sambas Terluar');
        $response->assertSee('Deviasi Geofence EXIF');
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

        $school = new School();
        $this->assertTrue(method_exists($school, 'damageReports'));

        $category = new FacilityCategory();
        $this->assertTrue(method_exists($category, 'damageReports'));
    }
}

