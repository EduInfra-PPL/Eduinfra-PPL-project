<?php

namespace App\Actions\Schools;

use App\Enums\ThreeTCategory;
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

    /**
     * @param  array{q?: string|null, level?: string|null, is_3t?: string|null, category?: string|null, status?: string|null}  $filters
     */
    public function execute(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        $status = $filters['status'] ?? 'aktif';
        $query->where('s.is_active', $status !== 'nonaktif');

        if (filled($filters['q'] ?? null)) {
            $q = trim((string) $filters['q']);
            $like = '%'.addcslashes($q, '\\%_').'%';
            $query->where(fn ($w) => $w->where('s.npsn', $q)->orWhere('s.name', 'ilike', $like));
        }
        if (filled($filters['level'] ?? null)) {
            $query->where('s.educational_level', $filters['level']);
        }
        if (filled($filters['is_3t'] ?? null)) {
            $query->where('r.is_3t', $filters['is_3t'] === '1');
        }
        if (filled($filters['category'] ?? null)) {
            $query->where('r.'.ThreeTCategory::from($filters['category'])->column(), true);
        }

        $page = $query->selectRaw(self::SELECT)
            ->orderBy('s.name')->orderBy('s.id')
            ->paginate($perPage)
            ->withQueryString();

        $page->through(fn (object $row): object => ThreeTCategory::decorate($row));

        return $page;
    }

    public function find(int $id): ?object
    {
        $row = $this->baseQuery()->where('s.id', $id)->selectRaw(self::SELECT)->first();

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

    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('schools as s')->join('regions as r', 'r.id', '=', 's.region_id');
    }
}
