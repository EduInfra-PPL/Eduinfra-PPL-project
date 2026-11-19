<?php

namespace Tests\Feature\Pendanaan;

use App\Models\AidCampaign;
use App\Models\AidContribution;
use App\Models\CostEstimate;
use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\Region;
use App\Models\RepairProject;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AidCampaignAndContributionTest extends TestCase
{
    use DatabaseTransactions;

    private function createVerifiedRepairProjectWithRab(int $rabAmount = 120_000_000): RepairProject
    {
        $user = User::factory()->create();

        $region = Region::query()->first() ?? Region::query()->create([
            'code' => '9999',
            'name' => 'Kab. Uji 3T',
            'level' => 'kab',
            'is_3t' => true,
            'is_tertinggal' => true,
            'is_terdepan' => false,
            'is_terluar' => false,
        ]);

        $school = School::query()->first() ?? School::query()->create([
            'region_id' => $region->id,
            'npsn' => '99' . random_int(100000, 999999),
            'name' => 'SDN Uji 3T Nusantara',
            'educational_level' => 'SD',
            'address' => 'Jl. Perbatasan No. 1',
            'is_active' => true,
        ]);

        $category = FacilityCategory::query()->first() ?? FacilityCategory::query()->create([
            'name' => 'Ruang Kelas',
            'sub_category' => 'Atap & Plafon',
            'urgency_weight' => 5,
            'default_unit' => 'Ruang',
            'is_enabled' => true,
        ]);

        $report = DamageReport::query()->create([
            'ticket_num' => 'TKT-TEST-' . random_int(10000, 99999),
            'school_id' => $school->id,
            'reporter_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Kerusakan Atap Ruang Kelas Uji',
            'description' => 'Atap bocor dan rangka lapuk.',
            'severity_level' => 4,
            'status' => 'verified',
        ]);

        $project = RepairProject::query()->create([
            'project_code' => 'PRJ-TEST-' . random_int(10000, 99999),
            'school_id' => $school->id,
            'report_id' => $report->id,
            'title' => 'Proyek Renovasi Uji 3T',
            'target_start' => now()->toDateString(),
            'target_end' => now()->addDays(90)->toDateString(),
            'progress_pct' => 0,
            'status' => 'in_progress',
        ]);

        CostEstimate::query()->create([
            'project_id' => $project->id,
            'item_name' => 'Pekerjaan Atap Baja Ringan',
            'quantity' => 1,
            'unit_price' => $rabAmount,
            'total_price' => $rabAmount,
            'approval_status' => 'Approved',
        ]);

        return $project;
    }

    public function test_guest_is_redirected_to_login_on_pendanaan_pages(): void
    {
        $this->get(route('pendanaan.index'))->assertRedirect(route('login'));
        $this->get(route('pendanaan.wall-of-donors'))->assertRedirect(route('login'));
        $this->post(route('pendanaan.store'), [])->assertRedirect(route('login'));
        $this->post(route('pendanaan.contributions.store'), [])->assertRedirect(route('login'));
    }

    public function test_home_route_renders_session_check_loading_page(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Memeriksa Sesi Anda');
    }

    public function test_session_expire_tab_endpoint_logs_out_and_invalidates_session(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('session.expire-tab'));

        $response->assertStatus(200)->assertJson(['expired' => true]);
        $this->assertTrue(Auth::guest());
    }

    public function test_pendanaan_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('pendanaan.index'));

        $response->assertStatus(200);
        $response->assertSee('Portal Kemitraan');
    }

    public function test_store_aid_campaign_locks_target_amount_from_domain3_rab_and_validates_duplicates(): void
    {
        $user = User::factory()->create();
        $project = $this->createVerifiedRepairProjectWithRab(120_000_000);

        // 1. Uji validasi deadline masa lalu
        $invalidResponse = $this->actingAs($user)->post(route('pendanaan.store'), [
            'project_id' => $project->id,
            'title' => 'Kampanye Uji Deadline Masa Lalu',
            'deadline' => now()->subDay()->toDateString(),
        ]);
        $invalidResponse->assertSessionHasErrors(['deadline']);

        // 2. Uji pembuatan valid + server-side RAB target lock (meskipun form mencoba kirim target_amount palsu)
        $validResponse = $this->actingAs($user)->post(route('pendanaan.store'), [
            'project_id' => $project->id,
            'title' => 'Kampanye Rekonstruksi Atap SD Uji 3T',
            'deadline' => now()->addDays(45)->toDateString(),
            'target_amount' => 1000, // Harus diabaikan dan dikunci ke 120_000_000
        ]);

        $validResponse->assertRedirect(route('pendanaan.index'));
        $validResponse->assertSessionHas('success');

        $campaign = AidCampaign::query()->where('project_id', $project->id)->first();
        $this->assertNotNull($campaign);
        $this->assertSame(120_000_000, $campaign->target_amount);
        $this->assertSame(0, $campaign->collected_amount);
        $this->assertSame('open', $campaign->status);

        // 3. Uji penolakan duplikasi project_id (relasi 1:1)
        $duplicateResponse = $this->actingAs($user)->post(route('pendanaan.store'), [
            'project_id' => $project->id,
            'title' => 'Kampanye Ganda',
            'deadline' => now()->addDays(30)->toDateString(),
        ]);
        $duplicateResponse->assertSessionHasErrors(['project_id']);
    }

    public function test_store_aid_contribution_records_donation_updates_campaign_and_marks_funded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = $this->createVerifiedRepairProjectWithRab(100_000_000);

        $campaign = AidCampaign::query()->create([
            'project_id' => $project->id,
            'title' => 'Kampanye Uji Kontribusi CSR',
            'target_amount' => 100_000_000,
            'collected_amount' => 0,
            'deadline' => now()->addDays(30)->toDateString(),
            'status' => 'open',
        ]);

        $proofFile = UploadedFile::fake()->create('bukti_mou_csr.pdf', 200, 'application/pdf');

        // 1. Kontribusi tahap pertama (50%)
        $response1 = $this->actingAs($user)->post(route('pendanaan.contributions.store'), [
            'campaign_id' => $campaign->id,
            'donor_name' => 'PT Telkom Indonesia Tbk',
            'donor_unit' => 'CSR Divre VII',
            'contribution_type' => 'cash',
            'amount_or_goods' => 50_000_000,
            'payment_proof' => $proofFile,
            'notes' => 'Komitmen tahap awal',
        ]);

        $response1->assertRedirect(route('pendanaan.index'));
        $response1->assertSessionHas('success');

        $campaign->refresh();
        $this->assertSame(50_000_000, $campaign->collected_amount);
        $this->assertSame('open', $campaign->status);

        $contribution = AidContribution::query()->where('campaign_id', $campaign->id)->first();
        $this->assertNotNull($contribution);
        $this->assertSame('verified', $contribution->status);
        $this->assertNotNull($contribution->payment_proof);
        Storage::disk('public')->assertExists($contribution->payment_proof);

        // 2. Kontribusi pelunasan (50% sisa) -> status kampanye otomatis berubah jadi 'funded'
        $response2 = $this->actingAs($user)->post(route('pendanaan.contributions.store'), [
            'campaign_id' => $campaign->id,
            'donor_name' => 'PT Bank Rakyat Indonesia Tbk',
            'donor_unit' => 'BRI Peduli',
            'contribution_type' => 'cash',
            'amount_or_goods' => 50_000_000,
        ]);

        $response2->assertRedirect(route('pendanaan.index'));
        $campaign->refresh();
        $this->assertSame(100_000_000, $campaign->collected_amount);
        $this->assertSame('funded', $campaign->status);
    }

    public function test_store_aid_contribution_validates_minimum_amount_and_file_type(): void
    {
        $user = User::factory()->create();
        $project = $this->createVerifiedRepairProjectWithRab(100_000_000);

        $campaign = AidCampaign::query()->create([
            'project_id' => $project->id,
            'title' => 'Kampanye Validasi Kontribusi',
            'target_amount' => 100_000_000,
            'collected_amount' => 0,
            'deadline' => now()->addDays(30)->toDateString(),
            'status' => 'open',
        ]);

        $invalidFile = UploadedFile::fake()->create('malicious.txt', 50, 'text/plain');

        $response = $this->actingAs($user)->post(route('pendanaan.contributions.store'), [
            'campaign_id' => $campaign->id,
            'donor_name' => 'PT Uji Validasi',
            'contribution_type' => 'invalid_type',
            'amount_or_goods' => 50_000, // di bawah minimum Rp 100.000
            'payment_proof' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['contribution_type', 'amount_or_goods', 'payment_proof']);
    }

    public function test_wall_of_donors_displays_dynamic_stats_contributors_and_audit_log(): void
    {
        $user = User::factory()->create();
        $project = $this->createVerifiedRepairProjectWithRab(100_000_000);

        $campaign = AidCampaign::query()->create([
            'project_id' => $project->id,
            'title' => 'Kampanye Wall of Donors Uji',
            'target_amount' => 100_000_000,
            'collected_amount' => 75_000_000,
            'deadline' => now()->addDays(30)->toDateString(),
            'status' => 'open',
        ]);

        AidContribution::query()->create([
            'transaction_code' => 'TRX-TEST-' . random_int(1000, 9999),
            'campaign_id' => $campaign->id,
            'donor_id' => $user->id,
            'donor_name' => 'PT Astra International Tbk',
            'donor_unit' => 'Yayasan Pendidikan Astra',
            'amount_or_goods' => 75_000_000,
            'contribution_type' => 'cash',
            'term_stage' => 'Termin 2 (Struktur)',
            'verification_note' => 'Pasang-Surut Selesai',
            'status' => 'verified',
        ]);

        $response = $this->actingAs($user)->get(route('pendanaan.wall-of-donors'));

        $response->assertStatus(200);
        $response->assertSee('Wall of Donors &amp; Penyaluran CSR', false);
        $response->assertSee('PT Astra International Tbk');
    }
}
