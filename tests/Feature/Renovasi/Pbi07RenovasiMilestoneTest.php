<?php

namespace Tests\Feature\Renovasi;

use App\Models\CostEstimate;
use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\ProjectMilestone;
use App\Models\Region;
use App\Models\RepairProject;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Pbi07RenovasiMilestoneTest extends TestCase
{
    use DatabaseTransactions;

    private function createAuthenticatedUser(): User
    {
        return User::factory()->create();
    }

    public function test_index_page_renders_master_data_projects_and_filters(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->get(route('renovasi.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Renovasi');
        $response->assertDontSee('Sekolah Dummy');

        $tableResponse = $this->actingAs($user)->get(route('renovasi.index', [
            'view' => 'table',
            'region' => '3t',
        ]));
        $tableResponse->assertStatus(200);
    }

    public function test_milestones_summary_page_renders_with_filters(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->get(route('renovasi.milestones', [
            'termin' => 'termin2',
            'curve' => 'ahead',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Monitoring Tahapan');
    }

    public function test_store_creates_repair_project_milestones_and_cost_estimates_from_damage_report(): void
    {
        $user = $this->createAuthenticatedUser();

        $region = Region::first() ?? Region::create([
            'code' => '64.11',
            'name' => 'Kab. Mahakam Ulu',
            'level' => 'kab',
            'is_3t_tier' => 1,
        ]);

        $school = School::first() ?? School::create([
            'npsn' => '30409988',
            'name' => 'SDN 001 Long Bagun',
            'educational_level' => 'SD',
            'region_id' => $region->id,
            'is_active' => true,
        ]);

        $category = FacilityCategory::first() ?? FacilityCategory::create([
            'name' => 'Ruang Kelas',
            'sub_category' => 'Struktur Atap',
            'urgency_weight' => 4.5,
            'is_enabled' => true,
        ]);

        $report = DamageReport::create([
            'ticket_num' => 'REP-2026-TEST07',
            'school_id' => $school->id,
            'category_id' => $category->id,
            'title' => 'Kerusakan Rangka Atap Ruang Kelas',
            'description' => 'Kuda-kuda kayu lapuk dan bocor saat hujan deras.',
            'severity_level' => 4,
            'status' => 'verified',
        ]);

        $response = $this->actingAs($user)->post(route('renovasi.store'), [
            'report_id' => $report->id,
            'school_id' => $school->id,
            'facility_category_id' => $category->id,
            'title' => 'Rehabilitasi Rangka Atap Ruang Kelas SDN 001 Long Bagun',
            'description' => 'Penggantian rangka atap baja ringan C75 SNI.',
            'priority' => 'high',
            'budget_allocated' => 250000000,
            'contractor_name' => 'CV Karya Mahakam Konstruksi',
            'surveyor_name' => 'Tim Teknis Dinas Pendidikan',
            'target_start' => '2026-10-01',
            'target_end' => '2027-01-30',
        ]);

        $project = RepairProject::where('report_id', $report->id)->first();
        $this->assertNotNull($project);
        $response->assertRedirect(route('renovasi.projects.show', $project->project_code));

        $this->assertEquals('in_repair', $report->fresh()->status);
        $this->assertCount(4, $project->milestones);
        $this->assertCount(4, $project->costEstimates);
        $this->assertEquals(250000000.0, (float) $project->costEstimates->sum('total_price'));

        // Verify Domain 4 compatibility query (repair_projects + cost_estimates + damage_reports)
        $approvedTotal = DB::table('cost_estimates')
            ->where('project_id', $project->id)
            ->whereIn('approval_status', ['Approved', 'approved'])
            ->sum('total_price');
        $this->assertEquals(250000000.0, (float) $approvedTotal);
    }

    public function test_update_milestone_recalculates_project_progress_and_syncs_rab_items(): void
    {
        Storage::fake('public');
        $user = $this->createAuthenticatedUser();

        $project = RepairProject::with(['milestones', 'costEstimates', 'damageReport'])->first();
        $this->assertNotNull($project);

        $milestone = $project->milestones->first();
        $estimateIds = $project->costEstimates->take(2)->pluck('id')->all();

        $response = $this->actingAs($user)
            ->from(route('renovasi.projects.show', $project->project_code))
            ->post(route('renovasi.milestones.update', $milestone->id), [
                'progress_pct' => 100,
                'status' => 'completed',
                'surveyor_name' => 'Ir. Hendra Pratama, ST.',
                'notes' => 'Tahap selesai 100% dan telah diperiksa di lapangan.',
                'sync_estimates' => '1',
                'completed_estimates' => $estimateIds,
                'proof_photo' => UploadedFile::fake()->create('bukti_fisik.jpg', 200, 'image/jpeg'),
            ]);

        $response->assertRedirect(route('renovasi.projects.show', $project->project_code));

        $milestone->refresh();
        $this->assertEquals(100.0, (float) $milestone->progress_pct);
        $this->assertEquals('completed', $milestone->status);
        $this->assertTrue((bool) $milestone->is_approved);
        $this->assertNotNull($milestone->proof_media_url);

        foreach ($estimateIds as $estId) {
            $this->assertTrue((bool) CostEstimate::find($estId)->is_completed);
        }
    }

    public function test_destroy_deletes_project_and_reverts_damage_report_status(): void
    {
        $user = $this->createAuthenticatedUser();

        $project = RepairProject::whereNotNull('report_id')->first();
        $this->assertNotNull($project);
        $reportId = $project->report_id;

        $response = $this->actingAs($user)->delete(route('renovasi.projects.destroy', $project->project_code));
        $response->assertRedirect(route('renovasi.index'));

        $this->assertDatabaseMissing('repair_projects', ['id' => $project->id]);
        $this->assertEquals('verified', DamageReport::find($reportId)->status);
    }
}
