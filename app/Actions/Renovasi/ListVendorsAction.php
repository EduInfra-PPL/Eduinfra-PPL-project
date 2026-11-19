<?php

namespace App\Actions\Renovasi;

use App\Models\Region;
use App\Models\RepairProject;
use App\Models\Vendor;

class ListVendorsAction
{
    public function execute(array $filters): array
    {
        $search = $filters['search'] ?? null;
        $grade = $filters['grade'] ?? null;
        $region = $filters['region'] ?? null;
        $status = $filters['status'] ?? null;
        $specialty = $filters['specialty'] ?? null;

        $vendors = Vendor::with([
            'serviceRegion',
            'repairProjects.school',
        ])
            ->search($search)
            ->filterGrade($grade)
            ->filterRegion($region)
            ->filterStatus($status)
            ->filterSpecialty($specialty)
            ->orderByDesc('is_active')
            ->orderByDesc('rating')
            ->orderBy('company_name')
            ->paginate(10)
            ->withQueryString();

        $allVendors = Vendor::with(['serviceRegion', 'repairProjects'])->get();
        $totalVendors = $allVendors->count();
        $activeVendors = $allVendors->where('is_active', true)->count();
        $reviewSbuCount = $allVendors->where('is_active', true)->where('sbu_status', 'review')->count();
        $blacklistCount = $allVendors->where('is_active', false)->count();

        $avgRating = $totalVendors > 0
            ? number_format((float) $allVendors->where('is_active', true)->avg('rating'), 2, '.', '')
            : '0.00';
        $avgOnTime = $totalVendors > 0
            ? round((float) $allVendors->where('is_active', true)->avg('on_time_pct'), 1)
            : 0.0;

        $activeProjectsCount = RepairProject::where('status', 'in_progress')->count();
        $readyToAssignCount = $allVendors->where('is_active', true)
            ->filter(fn (Vendor $v) => $v->repairProjects->where('status', 'in_progress')->count() < 3)
            ->count();

        $coveredRegionIds = $allVendors->pluck('service_region_id')->filter()->unique();
        $coveredRegionsCount = $coveredRegionIds->count();
        $covered3tCount = $allVendors->filter(fn (Vendor $v) => ($v->serviceRegion?->is_3t_tier ?? 0) > 0)
            ->pluck('service_region_id')
            ->filter()
            ->unique()
            ->count();

        $kpis = [
            'total_vendors' => [
                'val' => (string) $totalVendors,
                'unit' => 'Kontraktor',
                'badge' => "{$activeVendors} Aktif • {$reviewSbuCount} Penyesuaian SBU • {$blacklistCount} Blacklist",
            ],
            'avg_rating' => [
                'val' => $avgRating,
                'unit' => '/ 5.0',
                'badge' => "{$avgOnTime}% Ketepatan Mutu & Waktu BAST",
            ],
            'active_capacity' => [
                'val' => (string) $activeProjectsCount,
                'unit' => 'Proyek',
                'badge' => "{$readyToAssignCount} Rekanan Siap Ditugaskan",
            ],
            'region_coverage' => [
                'val' => (string) $coveredRegionsCount,
                'unit' => 'Kab/Kota',
                'badge' => "{$covered3tCount} Klaster Wilayah Prioritas 3T",
            ],
        ];

        $counts = [
            'all' => $totalVendors,
            'small' => $allVendors->whereIn('grade', ['K1', 'K2', 'K3'])->count(),
            'medium' => $allVendors->whereIn('grade', ['M1', 'M2'])->count(),
            'large' => $allVendors->whereIn('grade', ['B1', 'B2'])->count(),
            'sbu_review' => $reviewSbuCount,
            'blacklist' => $blacklistCount,
        ];

        $regions = Region::select(Region::COLUMNS)
            ->where('level', 'kab')
            ->orderByDesc('is_3t_tier')
            ->orderBy('name')
            ->get();

        $projects = RepairProject::with('school.region')
            ->orderByDesc('id')
            ->get();

        return compact(
            'vendors',
            'kpis',
            'counts',
            'regions',
            'projects',
            'search',
            'grade',
            'region',
            'status',
            'specialty'
        );
    }
}
