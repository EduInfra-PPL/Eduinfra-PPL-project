<?php

namespace App\Actions\FacilityCategories;

use App\Concerns\EscapesLikeWildcards;
use App\Models\FacilityCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Taksonomi fasilitas untuk halaman admin dan API (FR-03, R):
 * pencarian, filter, paginasi, detail satu kategori, dan angka ringkasan.
 */
class ListFacilityCategoriesAction
{
    use EscapesLikeWildcards;

    /**
     * @param  array{q?: string|null, name?: string|null, weight?: string|int|null, status?: string|null}  $filters
     */
    public function execute(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = FacilityCategory::query()->withCount('damageReports');

        $status = $filters['status'] ?? 'aktif';
        if ($status !== 'semua') {
            $query->where('is_enabled', $status !== 'nonaktif');
        }

        if (filled($filters['q'] ?? null)) {
            $like = '%'.$this->escapeLike(trim((string) $filters['q'])).'%';
            $query->where(fn (Builder $w) => $w->where('name', 'ilike', $like)->orWhere('sub_category', 'ilike', $like));
        }
        if (filled($filters['name'] ?? null)) {
            $query->where('name', $filters['name']);
        }
        if (filled($filters['weight'] ?? null)) {
            $query->where('urgency_weight', (int) $filters['weight']);
        }

        return $query->ordered()->orderBy('id')->paginate($perPage)->withQueryString();
    }

    /** Semua kategori untuk API (default hanya yang aktif). */
    public function all(bool $includeDisabled = false): Collection
    {
        return FacilityCategory::query()
            ->when(! $includeDisabled, fn (Builder $q) => $q->enabled())
            ->ordered()
            ->get(['id', 'name', 'sub_category', 'urgency_weight', 'default_unit', 'is_enabled']);
    }

    public function find(int $id): ?FacilityCategory
    {
        return FacilityCategory::query()->withCount('damageReports')->find($id);
    }

    /**
     * Nama kategori utama untuk filter dan saran isian.
     *
     * @return array<int, string>
     */
    public function groupNames(): array
    {
        return FacilityCategory::query()->distinct()->orderBy('name')->pluck('name')->all();
    }

    /**
     * Angka untuk kartu ringkasan dan sebaran bobot urgensi (kategori aktif).
     *
     * @return array{enabled: int, disabled: int, high: int, groups: int, by_weight: array<int, int>}
     */
    public function stats(): array
    {
        $row = DB::selectOne(
            'SELECT
                count(*) FILTER (WHERE is_enabled) AS enabled,
                count(*) FILTER (WHERE NOT is_enabled) AS disabled,
                count(*) FILTER (WHERE is_enabled AND urgency_weight >= ?) AS high,
                count(DISTINCT name) FILTER (WHERE is_enabled) AS groups
             FROM facility_categories',
            [FacilityCategory::HIGH_WEIGHT]
        );

        $byWeight = array_fill_keys(range(FacilityCategory::MIN_WEIGHT, FacilityCategory::MAX_WEIGHT), 0);
        $rows = FacilityCategory::query()->enabled()
            ->selectRaw('urgency_weight, count(*) AS total')
            ->groupBy('urgency_weight')
            ->toBase()
            ->get();
        foreach ($rows as $r) {
            $byWeight[(int) $r->urgency_weight] = (int) $r->total;
        }

        return [
            'enabled' => (int) $row->enabled,
            'disabled' => (int) $row->disabled,
            'high' => (int) $row->high,
            'groups' => (int) $row->groups,
            'by_weight' => $byWeight,
        ];
    }
}
