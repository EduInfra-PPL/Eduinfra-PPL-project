<?php

namespace Tests\Feature\Pendanaan;

use App\Models\CostEstimate;
use App\Models\DamageReport;
use App\Models\Disbursement;
use App\Models\FacilityCategory;
use App\Models\Region;
use App\Models\RepairProject;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DisbursementTest extends TestCase
{
    use DatabaseTransactions;

    private function createProjectWithApprovedRab(int $rabAmount = 200_000_000): RepairProject
    {
        $user = User::factory()->create();

        $region = Region::query()->first() ?? Region::query()->create([
            'code' => '9998',
            'name' => 'Kab. Uji Escrow 3T',
            'level' => 'kab',
            'is_3t' => true,
            'is_tertinggal' => true,
            'is_terdepan' => false,
            'is_terluar' => false,
        ]);

        $school = School::query()->first() ?? School::query()->create([
            'region_id' => $region->id,
            'npsn' => '98' . random_int(100000, 999999),
            'name' => 'SDN Uji Pencairan Escrow',
            'educational_level' => 'SD',
            'address' => 'Jl. Nusantara 3T No. 10',
            'is_active' => true,
        ]);

        $category = FacilityCategory::query()->first() ?? FacilityCategory::query()->create([
            'name' => 'Ruang Kelas',
            'sub_category' => 'Struktur & Atap',
            'urgency_weight' => 5,
            'default_unit' => 'Ruang',
            'is_enabled' => true,
        ]);

        $report = DamageReport::query()->create([
            'ticket_num' => 'TKT-DISB-' . random_int(10000, 99999),
            'school_id' => $school->id,
            'reporter_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Kerusakan Rangka Atap Uji Pencairan',
            'description' => 'Butuh pencairan bertahap vendor.',
            'severity_level' => 4,
            'status' => 'in_repair',
        ]);

        $project = RepairProject::query()->create([
            'project_code' => 'PRI-DISB-' . random_int(10000, 99999),
            'school_id' => $school->id,
            'report_id' => $report->id,
            'title' => 'Rehabilitasi Ruang Kelas Uji Escrow',
            'contractor_name' => 'CV Konstruksi Uji Nusantara',
            'budget_allocated' => $rabAmount,
            'budget_spent' => 0,
            'target_start' => now()->toDateString(),
            'target_end' => now()->addDays(90)->toDateString(),
            'progress_pct' => 30,
            'status' => 'in_progress',
        ]);

        CostEstimate::query()->create([
            'project_id' => $project->id,
            'item_name' => 'Pekerjaan Struktur & Atap Uji',
            'quantity' => 1,
            'unit_price' => $rabAmount,
            'total_price' => $rabAmount,
            'approval_status' => 'Approved',
        ]);

        return $project;
    }

    public function test_guest_is_redirected_to_login_on_disbursement_endpoints(): void
    {
        $project = $this->createProjectWithApprovedRab();
        $disbursement = Disbursement::query()->create([
            'transaction_code' => 'TRX-DISB-GUEST-' . random_int(100, 999),
            'project_id' => $project->id,
            'stage_num' => 1,
            'amount' => 60_000_000,
            'status' => 'disbursed',
            'disbursed_at' => now(),
        ]);

        $this->get(route('pendanaan.pencairan'))->assertRedirect(route('login'));
        $this->post(route('pendanaan.pencairan.store'), [])->assertRedirect(route('login'));
        $this->patch(route('pendanaan.pencairan.tax-invoice', $disbursement->id), [])->assertRedirect(route('login'));
        $this->delete(route('pendanaan.pencairan.destroy', $disbursement->id))->assertRedirect(route('login'));
    }

    public function test_pencairan_index_displays_kpis_projects_and_mutations_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('pendanaan.pencairan'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pencairan Anggaran');
        $response->assertSee('Catat Pencairan Dana ke Vendor');
    }

    public function test_store_disbursement_locks_stage_amount_from_rab_uploads_pdf_and_updates_budget_spent(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = $this->createProjectWithApprovedRab(200_000_000);

        $receipt = UploadedFile::fake()->create('kuitansi_termin1.pdf', 512, 'application/pdf');

        $response = $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 1,
            'amount' => 1_000, // Harus diabaikan dan dikunci ke 30% x 200jt = 60.000.000
            'receipt_file' => $receipt,
            'otp_code' => '849201',
        ]);

        $response->assertRedirect(route('pendanaan.pencairan'));
        $response->assertSessionHas('success');

        $disbursement = Disbursement::query()->where('project_id', $project->id)->first();
        $this->assertNotNull($disbursement);
        $this->assertSame(1, $disbursement->stage_num);
        $this->assertSame(60_000_000, $disbursement->amount);
        $this->assertSame('disbursed', $disbursement->status);
        $this->assertSame($user->id, $disbursement->authorized_by);
        $this->assertNotNull($disbursement->receipt_file);
        Storage::disk('public')->assertExists($disbursement->receipt_file);

        $project->refresh();
        $this->assertSame(60_000_000.0, (float) $project->budget_spent);
    }

    public function test_store_disbursement_draft_saves_without_otp_and_does_not_increment_budget_spent(): void
    {
        $user = User::factory()->create();
        $project = $this->createProjectWithApprovedRab(100_000_000);

        $response = $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 1,
            'submit_mode' => 'draft',
        ]);

        $response->assertRedirect(route('pendanaan.pencairan'));
        $response->assertSessionHas('success');

        $disbursement = Disbursement::query()->where('project_id', $project->id)->first();
        $this->assertNotNull($disbursement);
        $this->assertSame('draft', $disbursement->status);
        $this->assertNull($disbursement->disbursed_at);

        $project->refresh();
        $this->assertSame(0.0, (float) $project->budget_spent);
    }

    public function test_store_disbursement_enforces_sequential_stage_and_rejects_duplicate_and_invalid_inputs(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = $this->createProjectWithApprovedRab(100_000_000);
        $receipt = UploadedFile::fake()->create('kuitansi.pdf', 128, 'application/pdf');

        // 1. Tolak Termin 2 sebelum Termin 1 dicairkan
        $skipResponse = $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 2,
            'receipt_file' => $receipt,
            'otp_code' => '123456',
        ]);
        $skipResponse->assertSessionHasErrors(['stage_num']);

        // 2. Tolak file bukan PDF dan OTP tidak 6 digit
        $badFile = UploadedFile::fake()->create('malware.exe', 64, 'application/octet-stream');
        $invalidResponse = $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 1,
            'receipt_file' => $badFile,
            'otp_code' => '12A',
        ]);
        $invalidResponse->assertSessionHasErrors(['receipt_file', 'otp_code']);

        // 3. Cairkan Termin 1 secara sah lalu tolak duplikasi Termin 1
        $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 1,
            'receipt_file' => UploadedFile::fake()->create('t1.pdf', 128, 'application/pdf'),
            'otp_code' => '654321',
        ])->assertRedirect(route('pendanaan.pencairan'));

        $dupResponse = $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 1,
            'receipt_file' => UploadedFile::fake()->create('t1_dup.pdf', 128, 'application/pdf'),
            'otp_code' => '654321',
        ]);
        $dupResponse->assertSessionHasErrors(['stage_num']);
    }

    public function test_attach_tax_invoice_updates_disbursement_and_cancel_reverts_budget_spent(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = $this->createProjectWithApprovedRab(100_000_000);

        // Cairkan Termin 1 (30jt) tanpa faktur pajak awal
        $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 1,
            'receipt_file' => UploadedFile::fake()->create('kuitansi_t1.pdf', 120, 'application/pdf'),
            'otp_code' => '112233',
        ])->assertRedirect(route('pendanaan.pencairan'));

        $t1 = Disbursement::query()->where('project_id', $project->id)->where('stage_num', 1)->firstOrFail();
        $this->assertNull($t1->tax_invoice_file);

        // Update (U): Lampirkan Faktur Pajak Susulan
        $taxPdf = UploadedFile::fake()->create('efaktur_t1.pdf', 120, 'application/pdf');
        $updateResponse = $this->actingAs($user)->patch(route('pendanaan.pencairan.tax-invoice', $t1->id), [
            'tax_invoice_file' => $taxPdf,
        ]);
        $updateResponse->assertRedirect(route('pendanaan.pencairan'));
        $updateResponse->assertSessionHas('success');

        $t1->refresh();
        $this->assertNotNull($t1->tax_invoice_file);
        Storage::disk('public')->assertExists($t1->tax_invoice_file);

        // Cairkan Termin 2 (40jt) -> total budget_spent = 70jt
        $this->actingAs($user)->post(route('pendanaan.pencairan.store'), [
            'project_id' => $project->id,
            'stage_num' => 2,
            'receipt_file' => UploadedFile::fake()->create('kuitansi_t2.pdf', 120, 'application/pdf'),
            'otp_code' => '112233',
        ])->assertRedirect(route('pendanaan.pencairan'));

        $t2 = Disbursement::query()->where('project_id', $project->id)->where('stage_num', 2)->firstOrFail();
        $project->refresh();
        $this->assertSame(70_000_000.0, (float) $project->budget_spent);

        // Guard: Tolak anulir Termin 1 selama Termin 2 masih aktif
        $blockedCancel = $this->actingAs($user)->delete(route('pendanaan.pencairan.destroy', $t1->id));
        $blockedCancel->assertSessionHasErrors(['disbursement']);

        // Delete/Anulir (D): Anulir Termin 2 -> status menjadi cancelled & budget_spent kembali ke 30jt
        $cancelResponse = $this->actingAs($user)->delete(route('pendanaan.pencairan.destroy', $t2->id));
        $cancelResponse->assertRedirect(route('pendanaan.pencairan'));
        $cancelResponse->assertSessionHas('success');

        $t2->refresh();
        $project->refresh();
        $this->assertSame('cancelled', $t2->status);
        $this->assertNull($t2->disbursed_at);
        $this->assertSame(30_000_000.0, (float) $project->budget_spent);
    }
}
