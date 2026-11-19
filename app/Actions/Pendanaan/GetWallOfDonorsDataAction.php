<?php

namespace App\Actions\Pendanaan;

use App\Models\AidCampaign;
use App\Models\AidContribution;
use App\Services\Pendanaan\VerifiedProjectLookupService;
use Illuminate\Support\Facades\Storage;

/**
 * Action GetWallOfDonorsDataAction (Domain 4 — PBI-11).
 * Menghidrasi statistik KPI Wall of Donors, Honor Roll donatur korporasi,
 * dan Real-Time Escrow & Contribution Audit Log dari database.
 */
class GetWallOfDonorsDataAction
{
    public function __construct(
        protected VerifiedProjectLookupService $projectLookup
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $contributions = AidContribution::query()
            ->with('campaign')
            ->verified()
            ->orderByDesc('id')
            ->get();

        $projectIds = $contributions
            ->pluck('campaign.project_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $projectsMap = $this->projectLookup->getProjectsMapByIds($projectIds);

        // 1. Statistik KPI
        $totalAbsorbed = (int) $contributions->sum('amount_or_goods');
        $schoolsCount = $contributions->pluck('campaign_id')->unique()->count();
        $activeEscrow = (int) AidCampaign::query()->open()->sum('collected_amount');
        $activeProjectsCount = AidCampaign::query()->open()->count();
        $activeDonorsCount = $contributions->pluck('donor_name')->unique()->count();

        $stats = [
            'total_absorbed_formatted' => $this->formatShortRupiah($totalAbsorbed),
            'schools_count' => $schoolsCount,
            'students_impacted' => number_format($schoolsCount * 230, 0, ',', '.'),
            'active_escrow_formatted' => $this->formatShortRupiah($activeEscrow),
            'active_projects_count' => $activeProjectsCount,
            'active_donors_count' => $activeDonorsCount,
        ];

        // 2. Top Corporate Contributors (Honor Roll CSR)
        $topContributors = $contributions
            ->groupBy('donor_name')
            ->map(function ($group, string $donorName): array {
                $total = (int) $group->sum('amount_or_goods');
                $schools = $group->pluck('campaign_id')->unique()->count();
                $first = $group->first();

                $tier = match (true) {
                    $total >= 200_000_000 => 'Platinum',
                    $total >= 100_000_000 => 'Gold',
                    default => 'Silver',
                };

                $initials = collect(explode(' ', str_replace(['PT ', 'Tbk', 'Yayasan '], '', $donorName)))
                    ->filter()
                    ->take(2)
                    ->map(fn (string $word): string => strtoupper(substr($word, 0, 1)))
                    ->implode('');

                return [
                    'name' => $donorName,
                    'sector' => $first->donor_unit ?: 'Mitra Korporasi CSR',
                    'initials' => $initials ?: 'CS',
                    'tier' => $tier,
                    'allocation_raw' => $total,
                    'allocation_short' => $this->formatShortRupiah($total),
                    'allocation_full' => 'Rp ' . number_format($total, 0, ',', '.'),
                    'schools' => $schools,
                    'pmk_status' => 'Terbit BAP Kemendikbud',
                ];
            })
            ->sortByDesc('allocation_raw')
            ->values()
            ->take(6)
            ->all();

        // 3. Audit Log Table Rows
        $disbursements = $contributions->map(function (AidContribution $item) use ($projectsMap): array {
            $project = $item->campaign ? ($projectsMap[$item->campaign->project_id] ?? null) : null;

            $termKey = match (true) {
                str_contains(strtolower($item->term_stage), '3') || str_contains(strtolower($item->term_stage), 'final') => 'termin3',
                str_contains(strtolower($item->term_stage), '2') => 'termin2',
                default => 'termin1',
            };

            $verificationType = match ($termKey) {
                'termin3' => 'full',
                'termin2' => 'tide',
                default => 'drone',
            };

            $proofFileName = $item->payment_proof
                ? basename($item->payment_proof)
                : 'BAP_' . $item->transaction_code . '.pdf';

            $proofUrl = ($item->payment_proof && str_contains($item->payment_proof, '/'))
                ? Storage::disk('public')->url($item->payment_proof)
                : null;

            return [
                'code' => $item->transaction_code,
                'date' => $item->created_at ? $item->created_at->translatedFormat('d M Y') : now()->translatedFormat('d M Y'),
                'school' => $project['school_name'] ?? ($item->campaign->title ?? 'Sekolah Sasaran 3T'),
                'location' => $project['region_short'] ?? 'Wilayah 3T',
                'donor' => $item->donor_name,
                'unit' => $item->donor_unit ?: 'CSR Korporasi',
                'amount' => $item->amount_or_goods,
                'amount_formatted' => $item->formatted_amount,
                'stage' => $item->term_stage,
                'term_key' => $termKey,
                'verification_type' => $verificationType,
                'verification_label' => $item->verification_note ?: 'Terverifikasi BAP',
                'bap_file' => $proofFileName,
                'bap_url' => $proofUrl,
            ];
        })->values()->all();

        return [
            'stats' => $stats,
            'topContributors' => $topContributors,
            'disbursements' => $disbursements,
            'totalLogsCount' => count($disbursements),
        ];
    }

    protected function formatShortRupiah(int $amount): string
    {
        if ($amount >= 1_000_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000_000, 2, ',', '.') . ' M';
        }

        if ($amount >= 1_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000, 1, ',', '.') . ' Jt';
        }

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
