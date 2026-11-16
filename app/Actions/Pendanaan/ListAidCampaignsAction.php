<?php

namespace App\Actions\Pendanaan;

use App\Models\AidCampaign;
use App\Services\Pendanaan\VerifiedProjectLookupService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ListAidCampaignsAction
{
    public function __construct(
        protected VerifiedProjectLookupService $projectLookup
    ) {}

    /**
     * Ambil daftar kampanye CSR terhidrasi dengan metadata proyek + filter cepat.
     *
     * @return array{
     *     campaigns: Collection<int, array<string, mixed>>,
     *     availableProjects: Collection<int, array<string, mixed>>,
     *     counts: array{all: int, rusak_berat: int, under_50m: int, bantuan_fisik: int},
     *     isDomain3Ready: bool
     * }
     */
    public function execute(?string $keyword = null, string $filter = 'all'): array
    {
        $allRecords = Schema::hasTable('aid_campaigns')
            ? AidCampaign::query()->latest('id')->get()
            : collect();

        $projectIds = $allRecords->pluck('project_id')->map(fn ($id) => (int) $id)->all();
        $projectsMap = $this->projectLookup->getProjectsMapByIds($projectIds);

        $hydrated = $allRecords->map(function (AidCampaign $campaign) use ($projectsMap): array {
            $project = $projectsMap[$campaign->project_id] ?? $this->fallbackProjectInfo($campaign);

            return [
                'model' => $campaign,
                'id' => $campaign->id,
                'project_id' => $campaign->project_id,
                'title' => $campaign->title,
                'target_amount' => $campaign->target_amount,
                'collected_amount' => $campaign->collected_amount,
                'progress_pct' => $campaign->progress_percentage,
                'remaining_formatted' => $campaign->formatted_remaining,
                'target_formatted' => $campaign->formatted_target,
                'deadline' => $campaign->deadline,
                'status' => $campaign->status,
                'project' => $project,
            ];
        });

        $counts = [
            'all' => $hydrated->count(),
            'rusak_berat' => $hydrated->filter(fn (array $item): bool => (bool) ($item['project']['is_heavy_damage'] ?? false))->count(),
            'under_50m' => $hydrated->filter(fn (array $item): bool => $item['target_amount'] < 50_000_000)->count(),
            'bantuan_fisik' => $hydrated->filter(fn (array $item): bool => (bool) ($item['project']['is_physical_aid'] ?? true))->count(),
        ];

        $filtered = $this->applyFilters($hydrated, $keyword, $filter);

        return [
            'campaigns' => $filtered->values(),
            'availableProjects' => $this->projectLookup->getAvailableForCampaign(),
            'counts' => $counts,
            'isDomain3Ready' => $this->projectLookup->isDomain3Ready(),
        ];
    }

    /**
     * Terapkan pencarian teks dan chip filter cepat.
     *
     * @param Collection<int, array<string, mixed>> $items
     * @return Collection<int, array<string, mixed>>
     */
    protected function applyFilters(Collection $items, ?string $keyword, string $filter): Collection
    {
        if (! empty($keyword)) {
            $needle = mb_strtolower(trim($keyword));
            $items = $items->filter(function (array $item) use ($needle): bool {
                $haystack = mb_strtolower(implode(' ', [
                    $item['title'],
                    $item['project']['school_name'] ?? '',
                    $item['project']['location'] ?? '',
                    $item['project']['project_code'] ?? '',
                ]));

                return str_contains($haystack, $needle);
            });
        }

        return match ($filter) {
            'rusak_berat' => $items->filter(fn (array $item): bool => (bool) ($item['project']['is_heavy_damage'] ?? false)),
            'under_50m' => $items->filter(fn (array $item): bool => $item['target_amount'] < 50_000_000),
            'bantuan_fisik' => $items->filter(fn (array $item): bool => (bool) ($item['project']['is_physical_aid'] ?? true)),
            default => $items,
        };
    }

    /**
     * Fallback metadata proyek jika ada ID proyek lama yang belum ditemukan di map.
     *
     * @return array<string, mixed>
     */
    protected function fallbackProjectInfo(AidCampaign $campaign): array
    {
        return [
            'id' => $campaign->project_id,
            'project_code' => 'PRJ-' . date('Y') . '-' . str_pad((string) $campaign->project_id, 4, '0', STR_PAD_LEFT),
            'school_name' => 'Proyek Sekolah Sasaran #' . $campaign->project_id,
            'region_short' => 'Wilayah 3T Indonesia',
            'location' => 'Wilayah 3T Indonesia',
            'badge_label' => 'Bantuan Fisik',
            'is_heavy_damage' => false,
            'is_physical_aid' => true,
            'image_url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
        ];
    }
}
