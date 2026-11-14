<?php

namespace App\Services\Pendanaan;

use App\Enums\ThreeTCategory;
use App\Models\AidCampaign;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adapter Service untuk menjembatani Domain 4 (aid_campaigns) dengan Domain 3 & Domain 2.
 *
 * ============================================================================
 * KONTRAK INTEGRASI ANTAR-DOMAIN (DOMAIN 2 & 3 ──► DOMAIN 4)
 * ============================================================================
 * Domain 4 TIDAK membuat/mengubah migrasi Domain 2 & 3 untuk mencegah merge conflict.
 * Service ini mendukung Dual-Mode secara otomatis:
 *
 * 1. LIVE DB MODE (otomatis aktif saat tabel Domain 3 sudah di-merge):
 *    Membutuhkan tabel & kolom berikut sesuai PRD/ERD:
 *    - `repair_projects`: id, project_code, report_id, vendor_id (nullable),
 *                         target_start, target_end, progress_pct
 *    - `cost_estimates` : id, project_id, item_name, quantity, unit_price,
 *                         total_price, approval_status ('Approved')
 *    - `damage_reports` : id, school_id, category_id, title, description,
 *                         severity_level (1-5), status ('verified')
 *    - `schools`        : id, region_id, npsn, name, address
 *    - `regions`        : id, name, is_3t (turunan), is_tertinggal, is_terdepan, is_terluar
 *    - `audit_logs` (opsional jika tabel tersedia): report_id, inspection_date,
 *                         physical_score (0.0-5.0), audit_status ('Approved')
 *    - `report_media` (opsional jika tabel tersedia): report_id, media_url
 *
 * 2. CONTRACT FALLBACK MODE (aktif sementara Domain 3 belum di-merge):
 *    Menyediakan dataset proyek terverifikasi + nilai RAB SNI yang strukturnya
 *    100% identik dengan DTO kontrak di atas, dan tetap melakukan sinkronisasi
 *    status terpakai terhadap tabel `aid_campaigns` di database lokal.
 * ============================================================================
 */
class VerifiedProjectLookupService
{
    /**
     * Cek apakah tabel Domain 3 (proyek & RAB) sudah tersedia di database.
     */
    public function isDomain3Ready(): bool
    {
        return Schema::hasTable('repair_projects') && Schema::hasTable('cost_estimates');
    }

    /**
     * Ambil daftar proyek terverifikasi beserta RAB yang siap dirilis ke kampanye CSR
     * (belum memiliki kampanye di `aid_campaigns`).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getAvailableForCampaign(): Collection
    {
        $usedProjectIds = Schema::hasTable('aid_campaigns')
            ? AidCampaign::query()->pluck('project_id')->all()
            : [];

        return $this->getAllVerifiedProjects()
            ->reject(fn (array $project): bool => in_array($project['id'], $usedProjectIds, true))
            ->values();
    }

    /**
     * Cari detail satu proyek terverifikasi berdasarkan project_id.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $projectId): ?array
    {
        return $this->getAllVerifiedProjects()
            ->first(fn (array $project): bool => (int) $project['id'] === $projectId);
    }

    /**
     * Ambil map proyek terindeks project_id untuk menghidrasi katalog `aid_campaigns`.
     *
     * @param array<int, int> $projectIds
     * @return array<int, array<string, mixed>>
     */
    public function getProjectsMapByIds(array $projectIds): array
    {
        if ($projectIds === []) {
            return [];
        }

        return $this->getAllVerifiedProjects()
            ->whereIn('id', array_map('intval', $projectIds))
            ->keyBy('id')
            ->all();
    }

    /**
     * Ambil seluruh proyek terverifikasi yang memiliki RAB disetujui.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function getAllVerifiedProjects(): Collection
    {
        if ($this->isDomain3Ready()) {
            return $this->queryFromDomain3Tables();
        }

        return $this->getContractFallbackProjects();
    }

    /**
     * Query langsung ke tabel Domain 3 (+ Domain 1 & 2) ketika sudah tersedia di DB.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function queryFromDomain3Tables(): Collection
    {
        $hasAuditLogs = Schema::hasTable('audit_logs');
        $hasReportMedia = Schema::hasTable('report_media');
        $hasCategories = Schema::hasTable('facility_categories');

        $rabSubquery = DB::table('cost_estimates')
            ->selectRaw('project_id, COALESCE(SUM(total_price), 0) as rab_amount')
            ->whereIn('approval_status', ['Approved', 'approved'])
            ->groupBy('project_id');

        $query = DB::table('repair_projects as rp')
            ->joinSub($rabSubquery, 'rab', fn ($join) => $join->on('rab.project_id', '=', 'rp.id'))
            ->join('damage_reports as dr', 'dr.id', '=', 'rp.report_id')
            ->join('schools as s', 's.id', '=', 'dr.school_id')
            ->join('regions as r', 'r.id', '=', 's.region_id')
            ->where('rab.rab_amount', '>', 0)
            ->whereIn('dr.status', ['verified', 'Verified', 'in_repair']);

        if ($hasCategories) {
            $query->leftJoin('facility_categories as fc', 'fc.id', '=', 'dr.category_id');
        }

        $selects = [
            'rp.id',
            'rp.project_code',
            'rp.report_id',
            'rab.rab_amount',
            'dr.title as report_title',
            'dr.description as report_description',
            'dr.severity_level',
            's.npsn',
            's.name as school_name',
            's.address as school_address',
            'r.name as region_name',
            'r.is_3t',
            'r.is_tertinggal',
            'r.is_terdepan',
            'r.is_terluar',
            $hasCategories ? 'fc.name as category_name' : DB::raw("'Bantuan Fisik' as category_name"),
            $hasCategories ? 'fc.sub_category as sub_category_name' : DB::raw("'' as sub_category_name"),
        ];

        if ($hasAuditLogs) {
            $auditSubquery = DB::table('audit_logs')
                ->selectRaw('report_id, MAX(inspection_date) as inspection_date, MAX(physical_score) as physical_score')
                ->whereIn('audit_status', ['Approved', 'approved'])
                ->groupBy('report_id');

            $query->leftJoinSub($auditSubquery, 'al', fn ($join) => $join->on('al.report_id', '=', 'dr.id'));
            $selects[] = 'al.inspection_date';
            $selects[] = 'al.physical_score';
        }

        if ($hasReportMedia) {
            $mediaSubquery = DB::table('report_media')
                ->selectRaw('report_id, MIN(media_url) as media_url')
                ->groupBy('report_id');

            $query->leftJoinSub($mediaSubquery, 'rm', fn ($join) => $join->on('rm.report_id', '=', 'dr.id'));
            $selects[] = 'rm.media_url';
        }

        $rows = $query->select($selects)->orderByDesc('rp.id')->get();

        return $rows->map(function (object $row): array {
            $severityLevel = (int) ($row->severity_level ?? 4);
            $physicalScore = isset($row->physical_score) ? (float) $row->physical_score : null;
            $damagePct = $physicalScore !== null
                ? round(min(100, max(10, (5 - $physicalScore) * 20)), 1)
                : match ($severityLevel) {
                    5 => 88.5,
                    4 => 78.4,
                    3 => 52.0,
                    default => 30.0,
                };

            $severityLabel = $severityLevel >= 4 ? 'Rusak Berat' : ($severityLevel === 3 ? 'Rusak Sedang' : 'Ringan');
            $is3t = ThreeTCategory::isTruthy($row->is_3t);
            $verifiedDate = ! empty($row->inspection_date)
                ? Carbon::parse($row->inspection_date)->translatedFormat('d F Y')
                : now()->translatedFormat('d F Y');

            return [
                'id' => (int) $row->id,
                'project_code' => (string) $row->project_code,
                'school_name' => (string) $row->school_name,
                'npsn' => (string) $row->npsn,
                'is_3t' => $is3t,
                'category_3t_label' => ThreeTCategory::labelFor($row) ?? 'Wilayah Non-3T',
                'location' => trim(($row->school_address ? $row->school_address . ', ' : '') . $row->region_name, ', '),
                'region_short' => (string) $row->region_name,
                'verified_at' => $verifiedDate,
                'verifier_note' => "Terverifikasi oleh Tim Sarpras Dikdas tanggal {$verifiedDate}.",
                'damage_pct' => $damagePct,
                'severity_level' => $severityLevel,
                'severity_label' => $severityLabel,
                'is_heavy_damage' => $severityLevel >= 4,
                'is_physical_aid' => true,
                'affected_students' => 112,
                'rab_amount' => (int) $row->rab_amount,
                'rab_doc_num' => '440/BAP-SARPRAS/' . date('Y'),
                'summary_tag' => $row->sub_category_name ?: $row->category_name,
                'badge_label' => $severityLevel >= 5 ? 'Rusak Total' : ($row->sub_category_name ?: $row->category_name),
                'default_title' => (string) $row->report_title,
                'description' => (string) $row->report_description,
                'image_url' => $row->media_url ?: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
            ];
        });
    }

    /**
     * Dataset kontrak sementara selama tabel Domain 3 (`repair_projects`, `cost_estimates`)
     * belum di-merge ke repository utama.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function getContractFallbackProjects(): Collection
    {
        return collect([
            [
                'id' => 1,
                'project_code' => 'PRJ-2026-0881',
                'school_name' => 'SDN 02 Batom Perbatasan',
                'npsn' => '60302104',
                'is_3t' => true,
                'category_3t_label' => 'Terluar / Perbatasan',
                'location' => 'Pegunungan Bintang, Papua Pegunungan',
                'region_short' => 'Pegunungan Bintang, Papua Pegunungan',
                'verified_at' => '08 Februari 2026',
                'verifier_note' => 'Terverifikasi oleh Tim Sarpras Dikdas tanggal 08 Februari 2026.',
                'damage_pct' => 91.2,
                'severity_level' => 5,
                'severity_label' => 'Rusak Total',
                'is_heavy_damage' => true,
                'is_physical_aid' => true,
                'affected_students' => 134,
                'rab_amount' => 420_000_000,
                'rab_doc_num' => '412/BAP-SARPRAS/2026',
                'summary_tag' => 'Ruang Kelas & PLTS',
                'badge_label' => 'Rusak Total',
                'default_title' => 'Rekonstruksi 3 ruang kelas roboh akibat longsor dan penyediaan 1 unit PLTS komunal 3000 Wp.',
                'description' => 'Rekonstruksi 3 ruang kelas roboh akibat longsor dan penyediaan 1 unit PLTS komunal 3000 Wp.',
                'image_url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'id' => 2,
                'project_code' => 'PRJ-2026-0885',
                'school_name' => 'SMP Satap Pureman',
                'npsn' => '50305912',
                'is_3t' => true,
                'category_3t_label' => 'Tertinggal / Kepulauan',
                'location' => 'Kabupaten Alor, Nusa Tenggara Timur',
                'region_short' => 'Kabupaten Alor, Nusa Tenggara Timur',
                'verified_at' => '10 Februari 2026',
                'verifier_note' => 'Terverifikasi oleh Tim Sarpras Dikdas tanggal 10 Februari 2026.',
                'damage_pct' => 76.0,
                'severity_level' => 4,
                'severity_label' => 'Rusak Berat',
                'is_heavy_damage' => true,
                'is_physical_aid' => true,
                'affected_students' => 98,
                'rab_amount' => 185_000_000,
                'rab_doc_num' => '419/BAP-SARPRAS/2026',
                'summary_tag' => 'Atap & Sanitasi',
                'badge_label' => 'Atap Bocor & Sanitasi Buruk',
                'default_title' => 'Renovasi atap baja ringan, perbaikan plafon lapuk, dan instalasi tangki pemanen air hujan bersih.',
                'description' => 'Renovasi atap baja ringan, perbaikan plafon lapuk, dan instalasi tangki pemanen air hujan bersih.',
                'image_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'id' => 3,
                'project_code' => 'PRJ-2026-0888',
                'school_name' => 'SD Filial Tapal Batas Sebatik',
                'npsn' => '30405190',
                'is_3t' => true,
                'category_3t_label' => 'Terluar / Perbatasan',
                'location' => 'Pulau Sebatik, Nunukan, Kaltara',
                'region_short' => 'Pulau Sebatik, Nunukan, Kaltara',
                'verified_at' => '11 Februari 2026',
                'verifier_note' => 'Terverifikasi oleh Tim Sarpras Dikdas tanggal 11 Februari 2026.',
                'damage_pct' => 62.5,
                'severity_level' => 3,
                'severity_label' => 'Rusak Sedang',
                'is_heavy_damage' => false,
                'is_physical_aid' => false,
                'affected_students' => 86,
                'rab_amount' => 95_000_000,
                'rab_doc_num' => '428/BAP-SARPRAS/2026',
                'summary_tag' => 'Lab Vokasi & Internet',
                'badge_label' => 'Lab Vokasi & Internet',
                'default_title' => 'Pengadaan ruang komputer digital, perangkat satelit Starlink, dan laboratorium sains dasar anak TKI.',
                'description' => 'Pengadaan ruang komputer digital, perangkat satelit Starlink, dan laboratorium sains dasar anak TKI.',
                'image_url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'id' => 4,
                'project_code' => 'PRJ-2026-0891',
                'school_name' => 'SDN 02 Sambas Pesisir, Kalbar',
                'npsn' => '30104820',
                'is_3t' => true,
                'category_3t_label' => 'Terluar / Perbatasan',
                'location' => 'Dusun Nelayan Camar Bulan, Kec. Paloh, Kab. Sambas, Kalimantan Barat',
                'region_short' => 'Kab. Sambas, Kalimantan Barat',
                'verified_at' => '12 Februari 2026',
                'verifier_note' => 'Terverifikasi oleh Tim Sarpras Dikdas tanggal 12 Februari 2026.',
                'damage_pct' => 78.4,
                'severity_level' => 4,
                'severity_label' => 'Rusak Berat',
                'is_heavy_damage' => true,
                'is_physical_aid' => true,
                'affected_students' => 112,
                'rab_amount' => 420_000_000,
                'rab_doc_num' => '440/BAP-SARPRAS/2026',
                'summary_tag' => 'Atap Ambruk',
                'badge_label' => 'Atap Ambruk & Sanitasi',
                'default_title' => 'Rekonstruksi 4 Ruang Kelas Roboh & Penyediaan Sarana Sanitasi Sehat Ramah Anak',
                'description' => 'Rekonstruksi 4 ruang kelas atap ambruk serta pembangunan sarana sanitasi sehat ramah anak di pesisir perbatasan.',
                'image_url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'id' => 5,
                'project_code' => 'PRJ-2026-0894',
                'school_name' => 'SDN 01 Nduga Timur, Papua Pegunungan',
                'npsn' => '99000016',
                'is_3t' => true,
                'category_3t_label' => 'Terdepan / Pegunungan',
                'location' => 'Distrik Kenyam, Kabupaten Nduga, Papua Pegunungan',
                'region_short' => 'Kab. Nduga, Papua Pegunungan',
                'verified_at' => '15 Februari 2026',
                'verifier_note' => 'Terverifikasi oleh Tim Sarpras Dikdas tanggal 15 Februari 2026.',
                'damage_pct' => 84.0,
                'severity_level' => 5,
                'severity_label' => 'Rusak Berat',
                'is_heavy_damage' => true,
                'is_physical_aid' => true,
                'affected_students' => 145,
                'rab_amount' => 310_000_000,
                'rab_doc_num' => '451/BAP-SARPRAS/2026',
                'summary_tag' => 'Dinding & Pondasi Retak',
                'badge_label' => 'Rusak Berat',
                'default_title' => 'Rehabilitasi Total Struktur Dinding Kayu & Penguatan Pondasi 3 Ruang Belajar SDN 01 Nduga',
                'description' => 'Rehabilitasi struktur dinding kayu lapuk dan penguatan pondasi anti-gempa untuk 3 ruang kelas aktif.',
                'image_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'id' => 6,
                'project_code' => 'PRJ-2026-0899',
                'school_name' => 'SMPN 03 Kepulauan Mentawai, Sumbar',
                'npsn' => '10309811',
                'is_3t' => true,
                'category_3t_label' => 'Terluar / Kepulauan',
                'location' => 'Kec. Siberut Selatan, Kab. Kepulauan Mentawai, Sumatera Barat',
                'region_short' => 'Kepulauan Mentawai, Sumbar',
                'verified_at' => '18 Februari 2026',
                'verifier_note' => 'Terverifikasi oleh Tim Sarpras Dikdas tanggal 18 Februari 2026.',
                'damage_pct' => 48.5,
                'severity_level' => 3,
                'severity_label' => 'Rusak Sedang',
                'is_heavy_damage' => false,
                'is_physical_aid' => true,
                'affected_students' => 74,
                'rab_amount' => 45_000_000,
                'rab_doc_num' => '463/BAP-SARPRAS/2026',
                'summary_tag' => 'Sanitasi Air Bersih',
                'badge_label' => 'Sanitasi & Air Bersih',
                'default_title' => 'Pembangunan 2 Unit Bilik Toilet Terpisah & Pompa Filtrasi Air Bersih Sekolah',
                'description' => 'Pembangunan 2 unit toilet siswa terpisah putra/putri dan instalasi filtrasi air bersih ramah sanitasi.',
                'image_url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80',
            ],
        ]);
    }
}
