<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Memastikan tabel audit_logs memiliki seluruh kolom yang disyaratkan PBI-06 (SCRUM-32).
     */
    public function test_audit_logs_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('audit_logs'));

        $expectedColumns = [
            'id',
            'report_id',
            'auditor_id',
            'inspection_date',
            'physical_score',
            'audit_status',
            'notes',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('audit_logs', $column),
                "Column [{$column}] does not exist on [audit_logs] table."
            );
        }
    }

    /**
     * Memastikan relasi Eloquent timbal balik antara AuditLog, DamageReport, dan User berfungsi.
     */
    public function test_audit_log_and_damage_report_user_relationships(): void
    {
        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();
        $auditor = User::factory()->create();

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-AUDIT-01',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'reporter_id' => $auditor->id,
            'title' => 'Pengujian Relasi AuditLog PBI-06',
            'description' => 'Deskripsi pengujian relasi antara DamageReport, AuditLog, dan User.',
            'severity_level' => 4,
            'status' => 'pending',
        ]);

        $auditLog = AuditLog::create([
            'report_id' => $report->id,
            'auditor_id' => $auditor->id,
            'inspection_date' => '2026-11-27',
            'physical_score' => 4.25,
            'audit_status' => AuditLog::STATUS_APPROVED,
            'notes' => 'Hasil inspeksi lapangan menunjukkan kerusakan atap sesuai laporan.',
        ]);

        $this->assertInstanceOf(DamageReport::class, $auditLog->damageReport);
        $this->assertEquals($report->id, $auditLog->damageReport->id);

        $this->assertInstanceOf(User::class, $auditLog->auditor);
        $this->assertEquals($auditor->id, $auditLog->auditor->id);

        $this->assertTrue($report->auditLogs->contains('id', $auditLog->id));
        $this->assertTrue($auditor->auditLogs->contains('id', $auditLog->id));
    }

    /**
     * Memastikan query scopes dan accessors label/badge pada model AuditLog akurat.
     */
    public function test_audit_log_scopes_and_accessors(): void
    {
        $school = School::active()->first();
        $category = FacilityCategory::where('is_enabled', true)->first();
        $auditor = User::factory()->create();

        $report = DamageReport::create([
            'ticket_num' => 'RPT-TEST-AUDIT-02',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'reporter_id' => $auditor->id,
            'title' => 'Pengujian Scopes dan Accessor AuditLog',
            'description' => 'Deskripsi pengujian scopes status dan accessor label pada AuditLog.',
            'severity_level' => 3,
            'status' => 'pending',
        ]);

        $approved = AuditLog::create([
            'report_id' => $report->id,
            'auditor_id' => $auditor->id,
            'inspection_date' => '2026-11-27',
            'physical_score' => 4.50,
            'audit_status' => AuditLog::STATUS_APPROVED,
            'notes' => 'Disetujui untuk tahap renovasi.',
        ]);

        $needsReview = AuditLog::create([
            'report_id' => $report->id,
            'auditor_id' => $auditor->id,
            'inspection_date' => '2026-11-27',
            'physical_score' => 3.00,
            'audit_status' => AuditLog::STATUS_NEEDS_REVIEW,
            'notes' => 'Membutuhkan dokumen foto tambahan.',
        ]);

        $rejected = AuditLog::create([
            'report_id' => $report->id,
            'auditor_id' => $auditor->id,
            'inspection_date' => '2026-11-27',
            'physical_score' => 1.25,
            'audit_status' => AuditLog::STATUS_REJECTED,
            'notes' => 'Laporan tidak sesuai kondisi lapangan.',
        ]);

        $this->assertTrue(AuditLog::approved()->where('id', $approved->id)->exists());
        $this->assertTrue(AuditLog::needsReview()->where('id', $needsReview->id)->exists());
        $this->assertTrue(AuditLog::rejected()->where('id', $rejected->id)->exists());

        $this->assertEquals('Disetujui (Verified)', $approved->status_label);
        $this->assertStringContainsString('emerald', $approved->status_badge_class);

        $this->assertEquals('Perlu Tinjauan Ulang', $needsReview->status_label);
        $this->assertStringContainsString('amber', $needsReview->status_badge_class);

        $this->assertEquals('Ditolak', $rejected->status_label);
        $this->assertStringContainsString('rose', $rejected->status_badge_class);
    }
}
