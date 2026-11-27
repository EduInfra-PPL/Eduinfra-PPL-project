<?php

namespace App\Actions\Pendanaan;

use App\Models\AidContribution;
use App\Models\CostEstimate;
use App\Models\Disbursement;
use App\Models\RepairProject;
use Illuminate\Support\Facades\Schema;

/**
 * Action ListDisbursementsAction (Domain 4 — PBI-12 Read).
 * Menghidrasi KPI pencairan anggaran, pilihan proyek beserta status terminnya,
 * serta riwayat mutasi `disbursements` dari database PostgreSQL.
 */
class ListDisbursementsAction
{
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $allDisbursements = Disbursement::query()
            ->with(['project.school.region', 'authorizer'])
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('disbursed_at')
            ->orderByDesc('id')
            ->get();

        $disbursedOnly = $allDisbursements->where('status', 'disbursed');
        $pendingOnly = $allDisbursements->where('status', 'pending_approval');

        $totalDisbursed = (int) $disbursedOnly->sum('amount');
        $monthlyDisbursed = (int) $disbursedOnly
            ->filter(fn (Disbursement $d): bool => $d->disbursed_at && $d->disbursed_at->isCurrentMonth())
            ->sum('amount');
        $monthlyCount = $disbursedOnly
            ->filter(fn (Disbursement $d): bool => $d->disbursed_at && $d->disbursed_at->isCurrentMonth())
            ->count();

        $totalPortfolioRab = (int) CostEstimate::query()
            ->whereIn('approval_status', ['Approved', 'approved'])
            ->sum('total_price');
        $portfolioPct = $totalPortfolioRab > 0
            ? round(min(100, ($totalDisbursed / $totalPortfolioRab) * 100), 1)
            : 0.0;

        $totalActiveCount = $allDisbursements->count();
        $taxVerifiedCount = $allDisbursements->whereNotNull('tax_invoice_file')->count();
        $taxVerifiedPct = $totalActiveCount > 0
            ? round(($taxVerifiedCount / $totalActiveCount) * 100, 1)
            : 100.0;

        $stats = [
            'total_disbursed_formatted' => 'Rp ' . number_format($totalDisbursed, 0, ',', '.'),
            'portfolio_pct' => $portfolioPct,
            'total_batches' => $disbursedOnly->count(),
            'monthly_disbursed_formatted' => 'Rp ' . number_format($monthlyDisbursed, 0, ',', '.'),
            'monthly_count' => $monthlyCount,
            'pending_count' => $pendingOnly->count(),
            'pending_amount_formatted' => 'Rp ' . number_format((int) $pendingOnly->sum('amount'), 0, ',', '.'),
            'tax_verified_pct' => $taxVerifiedPct,
        ];

        $totalContributions = Schema::hasTable('aid_contributions')
            ? (int) AidContribution::query()->where('status', 'verified')->sum('amount_or_goods')
            : 0;

        $escrowSummary = [
            'balance_formatted' => 'Rp ' . number_format(max(0, $totalContributions - $totalDisbursed), 0, ',', '.'),
            'bank_name' => 'PT Bank Mandiri (Persero) Tbk',
        ];

        // Daftar Proyek Sasaran untuk Form Pencairan
        $projects = RepairProject::query()
            ->with(['school.region', 'disbursements' => fn ($q) => $q->where('status', '!=', 'cancelled')])
            ->orderByDesc('id')
            ->get()
            ->map(function (RepairProject $project): array {
                $rabTotal = Disbursement::resolveProjectRabTotal($project);
                $stageAmounts = Disbursement::calculateStageAmounts($rabTotal);

                $paidByStage = $project->disbursements
                    ->where('status', 'disbursed')
                    ->keyBy('stage_num');

                $stages = [];
                foreach ($stageAmounts as $num => $stageAmount) {
                    $paidRecord = $paidByStage->get($num);
                    $stageTitle = match ($num) {
                        1 => 'Termin 1 (Uang Muka 30%) – Pondasi & Struktur',
                        2 => 'Termin 2 (Progress 40%) – Rangka Atap & Finishing',
                        3 => 'Termin 3 (Pelunasan 30%) – BAST Akhir & Uji Fungsi',
                    };

                    if ($paidRecord) {
                        $dateStr = $paidRecord->disbursed_at
                            ? $paidRecord->disbursed_at->translatedFormat('d M Y')
                            : $paidRecord->created_at->translatedFormat('d M Y');
                        $stages[$num] = [
                            'title' => $stageTitle,
                            'note' => "Telah Dicairkan pada {$dateStr}",
                            'amount' => (int) $paidRecord->amount,
                            'status' => 'paid',
                        ];
                    } else {
                        $prevPaid = $num === 1 || $paidByStage->has($num - 1);
                        $stages[$num] = [
                            'title' => $stageTitle,
                            'note' => $prevPaid
                                ? 'Siap Dicairkan (Audit Fisik Memenuhi Syarat)'
                                : 'Menunggu Penyelesaian Termin Sebelumnya',
                            'amount' => $stageAmount,
                            'status' => $prevPaid ? 'ready' : 'locked',
                        ];
                    }
                }

                $schoolName = $project->school?->name ?? 'Sekolah 3T';
                $regionName = $project->school?->region?->name ?? 'Wilayah 3T';
                $contractor = $project->contractor_name ?: 'Kontraktor Pelaksana';

                return [
                    'id' => $project->id,
                    'code' => $project->project_code,
                    'label' => "{$project->project_code} — {$schoolName} ({$regionName}) | Kontraktor: {$contractor}",
                    'title' => $project->title,
                    'donor' => $project->consortium_name ?: 'Mitra CSR EduInfra 3T',
                    'progress' => number_format((float) $project->progress_pct, 1) . '%',
                    'stages' => $stages,
                ];
            })
            ->values()
            ->all();

        // Daftar Mutasi untuk Tabel Kanan
        $mutations = $allDisbursements->map(function (Disbursement $d): array {
            $schoolName = $d->project?->school?->name ?? ($d->project?->title ?? 'Sekolah Sasaran 3T');
            $projectCode = $d->project?->project_code ?? '-';
            $progress = (float) ($d->project?->progress_pct ?? ($d->stage_num === 3 ? 100 : ($d->stage_num === 2 ? 60 : 30)));

            return [
                'id' => $d->id,
                'code' => $d->transaction_code,
                'datetime' => $d->disbursed_at
                    ? $d->disbursed_at->translatedFormat('d M Y • H:i') . ' WIB'
                    : $d->created_at->translatedFormat('d M Y • H:i') . ' WIB',
                'school' => $schoolName,
                'project_code' => $projectCode,
                'vendor' => $d->vendor_name ?: ($d->project?->contractor_name ?: 'Kontraktor Pelaksana'),
                'bank' => $d->vendor_bank_account ?: 'Bank Mandiri •• 8821',
                'stage_num' => $d->stage_num,
                'stage_label' => $d->stage_label,
                'status' => $d->status,
                'amount_formatted' => $d->formatted_amount,
                'progress' => round($progress, 1),
                'needs_tax_invoice' => $d->tax_invoice_file === null,
                'tax_invoice_file' => $d->tax_invoice_file,
                'update_tax_url' => route('pendanaan.pencairan.tax-invoice', $d->id),
                'cancel_url' => route('pendanaan.pencairan.destroy', $d->id),
            ];
        })->values()->all();

        $counts = [
            'all' => $allDisbursements->count(),
            'termin1' => $allDisbursements->where('stage_num', 1)->count(),
            'termin2' => $allDisbursements->where('stage_num', 2)->count(),
            'termin3' => $allDisbursements->where('stage_num', 3)->count(),
            'tax_pending' => $allDisbursements->whereNull('tax_invoice_file')->count(),
        ];

        return [
            'stats' => $stats,
            'escrowSummary' => $escrowSummary,
            'projects' => $projects,
            'mutations' => $mutations,
            'counts' => $counts,
        ];
    }
}
