<?php

namespace App\Actions\Renovasi;

use App\Models\CostEstimate;
use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\ProjectMilestone;
use App\Models\Region;
use App\Models\RepairProject;
use App\Models\School;

class ListRepairProjectsAction
{
    public function getIndexPayload(array $filters): array
    {
        $viewType = ($filters['view'] ?? 'grid') === 'table' ? 'table' : 'grid';
        $search = $filters['search'] ?? null;
        $status = $filters['status'] ?? null;
        $region = $filters['region'] ?? null;

        $projects = RepairProject::with([
            'school.region',
            'damageReport.category',
            'milestones',
            'costEstimates.facilityCategory',
        ])
            ->search($search)
            ->filterStatus($status)
            ->filterRegion($region)
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        $allProjects = RepairProject::with(['school.region', 'costEstimates'])->get();
        $totalCount = $allProjects->count();
        $activeCount = $allProjects->where('status', 'in_progress')->count();
        $completedCount = $allProjects->where('status', 'completed')->count();

        $totalBudget = (float) $allProjects->sum(fn (RepairProject $p) => $p->effective_budget);
        $formattedRab = $this->formatCompactCurrency($totalBudget);
        $approvedRabRows = CostEstimate::whereIn('approval_status', ['Approved', 'approved'])->count();

        $avgProgress = $totalCount > 0 ? round((float) $allProjects->avg('progress_pct'), 1) : 0.0;
        $onTrackCount = $allProjects->filter(fn (RepairProject $p) => $p->progress_pct >= ($p->target_pct - 5))->count();

        $distinctContractors = $allProjects->pluck('contractor_name')->filter()->unique()->count();
        $coveredRegions = $allProjects->pluck('school.region_id')->filter()->unique()->count();

        $kpis = [
            'total_projects' => [
                'val' => (string) $totalCount,
                'unit' => 'Proyek',
                'badge' => "{$activeCount} Aktif • {$completedCount} Selesai",
            ],
            'total_rab' => [
                'val' => $formattedRab['val'],
                'unit' => $formattedRab['unit'],
                'badge' => "{$approvedRabRows} Item RAB Terverifikasi",
            ],
            'avg_progress' => [
                'val' => $avgProgress . '%',
                'unit' => '',
                'badge' => "{$onTrackCount} dari {$totalCount} Proyek Sesuai Kurva S",
            ],
            'total_vendors' => [
                'val' => (string) $distinctContractors,
                'unit' => 'Kontraktor',
                'badge' => "Tersebar di {$coveredRegions} Wilayah",
            ],
        ];

        $verifiedReports = DamageReport::with(['school.region', 'category'])
            ->where('status', 'verified')
            ->latest('id')
            ->get();

        $schools = School::active()
            ->with('region')
            ->orderBy('name')
            ->limit(1000)
            ->get();

        $categories = FacilityCategory::where('is_enabled', true)
            ->orderByDesc('urgency_weight')
            ->orderBy('name')
            ->get();

        $regions = Region::select(Region::COLUMNS)
            ->where('level', 'kab')
            ->orderByDesc('is_3t_tier')
            ->orderBy('name')
            ->get();

        return compact(
            'projects',
            'kpis',
            'viewType',
            'verifiedReports',
            'schools',
            'categories',
            'regions',
            'search',
            'status',
            'region',
            'totalCount'
        );
    }

    public function getMilestonesPayload(array $filters): array
    {
        $search = $filters['search'] ?? null;
        $termin = $filters['termin'] ?? null;
        $curve = $filters['curve'] ?? null;
        $region = $filters['region'] ?? null;

        $projects = RepairProject::with([
            'school.region',
            'damageReport.category',
            'milestones',
            'costEstimates.facilityCategory',
        ])
            ->search($search)
            ->filterStatus($termin)
            ->filterCurve($curve)
            ->filterRegion($region)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $allProjects = RepairProject::with(['milestones', 'costEstimates'])->get();
        $totalMilestones = ProjectMilestone::count();
        $completedMilestones = ProjectMilestone::where('status', 'completed')->count();
        $inProgressMilestones = ProjectMilestone::where('status', 'in_progress')->count();

        $avgDeviation = $allProjects->isNotEmpty()
            ? round((float) $allProjects->avg(fn (RepairProject $p) => $p->progress_pct - $p->target_pct), 1)
            : 0.0;
        $onTrackProjects = $allProjects->filter(fn (RepairProject $p) => $p->progress_pct >= ($p->target_pct - 5))->count();

        $readyProjects = $allProjects->filter(fn (RepairProject $p) => $p->escrow_ready);
        $readyEscrowSum = (float) $readyProjects->sum(fn (RepairProject $p) => $p->termin_release_amount);
        $formattedEscrow = $this->formatCompactCurrency($readyEscrowSum);

        $totalRabItems = CostEstimate::count();
        $completedRabItems = CostEstimate::where('is_completed', true)->count();
        $rabCompletionPct = $totalRabItems > 0 ? round(($completedRabItems / $totalRabItems) * 100, 1) : 0.0;

        $kpis = [
            'total_milestones' => [
                'val' => (string) $totalMilestones,
                'unit' => 'Milestone',
                'badge' => "{$completedMilestones} Selesai • {$inProgressMilestones} Berjalan",
            ],
            'deviasi_kurva' => [
                'val' => ($avgDeviation >= 0 ? '+' : '') . $avgDeviation . '%',
                'unit' => '',
                'badge' => "{$onTrackProjects} Proyek Sesuai Jadwal",
            ],
            'escrow_cair' => [
                'val' => $formattedEscrow['val'],
                'unit' => $formattedEscrow['unit'],
                'badge' => $readyProjects->count() . ' Proyek Memenuhi Target Termin',
            ],
            'geotag_compliance' => [
                'val' => $rabCompletionPct . '%',
                'unit' => '',
                'badge' => "{$completedRabItems} dari {$totalRabItems} Item RAB Tuntas",
            ],
        ];

        $counts = [
            'all' => $allProjects->count(),
            'termin1' => $allProjects->filter(fn (RepairProject $p) => $p->progress_pct < 30 && $p->status !== 'completed')->count(),
            'termin2' => $allProjects->filter(fn (RepairProject $p) => $p->progress_pct >= 30 && $p->progress_pct < 90 && $p->status !== 'completed')->count(),
            'termin3' => $allProjects->filter(fn (RepairProject $p) => $p->progress_pct >= 90 || $p->status === 'completed')->count(),
            'escrow_ready' => $readyProjects->count(),
            'delayed' => $allProjects->filter(fn (RepairProject $p) => $p->progress_pct < $p->target_pct || $p->milestones->contains('status', 'delayed'))->count(),
        ];

        $regions = Region::select(Region::COLUMNS)
            ->where('level', 'kab')
            ->orderByDesc('is_3t_tier')
            ->orderBy('name')
            ->get();

        $verifiedReports = DamageReport::with(['school.region', 'category'])
            ->where('status', 'verified')
            ->latest('id')
            ->get();

        $schools = School::active()
            ->with('region')
            ->orderBy('name')
            ->limit(1000)
            ->get();

        $categories = FacilityCategory::where('is_enabled', true)
            ->orderByDesc('urgency_weight')
            ->orderBy('name')
            ->get();

        return compact(
            'projects',
            'kpis',
            'counts',
            'regions',
            'verifiedReports',
            'schools',
            'categories',
            'search',
            'termin',
            'curve',
            'region'
        );
    }

    private function formatCompactCurrency(float $amount): array
    {
        if ($amount >= 1_000_000_000) {
            return [
                'val' => 'Rp ' . number_format($amount / 1_000_000_000, 2, ',', '.'),
                'unit' => 'Miliar',
            ];
        }

        if ($amount >= 1_000_000) {
            return [
                'val' => 'Rp ' . number_format($amount / 1_000_000, 1, ',', '.'),
                'unit' => 'Juta',
            ];
        }

        return [
            'val' => 'Rp ' . number_format($amount, 0, ',', '.'),
            'unit' => '',
        ];
    }
}
