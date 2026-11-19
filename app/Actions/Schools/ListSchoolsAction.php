<?php

namespace App\Actions\Schools;

use App\Enums\ThreeTCategory;
use App\Services\Schools\SchoolQueryService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Daftar sekolah untuk halaman admin PBI-01: pencarian, filter, paginasi, dan detail satu sekolah.
 */
class ListSchoolsAction
{
    private const SELECT = 's.id, s.npsn, s.name, s.educational_level, s.address, s.accreditation,
        s.contact_phone, s.contact_email, s.is_active, s.source,
        ST_Y(s.coordinates::geometry) AS lat, ST_X(s.coordinates::geometry) AS lng,
        r.id AS region_id, r.code AS region_code, r.name AS region_name,
        r.is_3t, r.is_tertinggal, r.is_terdepan, r.is_terluar';

    public function __construct(private SchoolQueryService $schools) {}

    /**
     * @param  array{q?: string|null, level?: string|null, is_3t?: string|null, category?: string|null, status?: string|null}  $filters
     */
    public function execute(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $status = $filters['status'] ?? 'aktif';
        $query = $this->schools->baseQuery()->where('s.is_active', $status !== 'nonaktif');

        $this->schools->applyFilters($query, [
            'q' => filled($filters['q'] ?? null) ? trim((string) $filters['q']) : null,
            'level' => $filters['level'] ?? null,
            'is_3t' => filled($filters['is_3t'] ?? null) ? $filters['is_3t'] === '1' : null,
            'category' => $filters['category'] ?? null,
        ]);

        return $query->selectRaw(self::SELECT)
            ->orderBy('s.name')->orderBy('s.id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (object $row): object => ThreeTCategory::decorate($row));
    }

    public function find(int $id): ?object
    {
        $row = $this->schools->baseQuery()->where('s.id', $id)->selectRaw(self::SELECT)->first();

        return $row ? ThreeTCategory::decorate($row) : null;
    }

    /**
     * Angka untuk kartu ringkasan di atas halaman.
     *
     * @return array{total: int, three_t: int, regencies: int, inactive: int}
     */
    public function stats(): array
    {
        $row = DB::selectOne(
            'SELECT
                (SELECT count(*) FROM schools WHERE is_active) AS total,
                (SELECT count(*) FROM schools s JOIN regions r ON r.id = s.region_id WHERE s.is_active AND r.is_3t) AS three_t,
                (SELECT count(*) FROM regions WHERE level = \'kab\') AS regencies,
                (SELECT count(*) FROM schools WHERE NOT is_active) AS inactive'
        );

        return [
            'total' => (int) $row->total,
            'three_t' => (int) $row->three_t,
            'regencies' => (int) $row->regencies,
            'inactive' => (int) $row->inactive,
        ];
    }
}
