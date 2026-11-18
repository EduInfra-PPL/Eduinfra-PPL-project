<?php

namespace App\Services\Schools;

use App\Concerns\EscapesLikeWildcards;
use App\Enums\ThreeTCategory;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Query baca sekolah (PBI-01 / FR-01, FR-17): pencarian, detail, sekolah terdekat, dan GeoJSON.
 * Dipakai API v1; filter pencarian juga dipakai halaman admin (ListSchoolsAction).
 */
class SchoolQueryService
{
    use EscapesLikeWildcards;

    /** Kolom respons API; kolom wilayah 3T ikut dibaca setiap kali sekolah ditampilkan. */
    private const API_SELECT = 's.id, s.npsn, s.name, s.educational_level, s.address, s.accreditation,
        s.contact_phone, s.contact_email, s.is_active,
        ST_Y(s.coordinates::geometry) AS lat, ST_X(s.coordinates::geometry) AS lng,
        r.id AS region_id, r.code AS region_code, r.name AS region_name, r.is_3t_tier,
        r.is_3t, r.is_tertinggal, r.is_terdepan, r.is_terluar';

    /** Titik dari (lng, lat) - urutan bind: lng, lat. */
    private const POINT = 'ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography';

    /** schools s JOIN regions r. */
    public function baseQuery(): Builder
    {
        return DB::table('schools as s')->join('regions as r', 'r.id', '=', 's.region_id');
    }

    /**
     * Terapkan filter pencarian ke query baseQuery().
     *
     * @param  array{q?: ?string, level?: ?string, region_id?: ?int, region_code?: ?string, is_3t?: ?bool, category?: ?string}  $filters
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        if (filled($filters['q'] ?? null)) {
            $term = (string) $filters['q'];
            $like = '%'.$this->escapeLike($term).'%';
            $query->where(fn (Builder $w) => $w->where('s.npsn', $term)->orWhere('s.name', 'ilike', $like));
        }
        if (filled($filters['level'] ?? null)) {
            $query->where('s.educational_level', $filters['level']);
        }
        if (filled($filters['region_id'] ?? null)) {
            $query->where('s.region_id', (int) $filters['region_id']);
        }
        if (filled($filters['region_code'] ?? null)) {
            // kode provinsi adalah awalan kode kabupaten: 53 -> 53.07
            $query->where('r.code', 'like', $this->escapeLike($filters['region_code']).'%');
        }
        if (isset($filters['is_3t'])) {
            $query->where('r.is_3t', $filters['is_3t']);
        }
        if (filled($filters['category'] ?? null)) {
            $query->where('r.'.ThreeTCategory::from($filters['category'])->column(), true);
        }

        return $query;
    }

    /** Sekolah aktif yang cocok dengan filter, urut nama, sudah didekorasi kategori 3T. */
    public function paginateActive(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->applyFilters($this->baseQuery()->where('s.is_active', true), $filters);

        $page = $query->selectRaw(self::API_SELECT)
            ->orderBy('s.name')->orderBy('s.id')
            ->paginate($perPage);

        return $page->through(fn (object $row): object => ThreeTCategory::decorate($row));
    }

    /** Satu sekolah aktif (format respons API), atau null. */
    public function findActive(int $id): ?object
    {
        $row = $this->baseQuery()
            ->where('s.id', $id)->where('s.is_active', true)
            ->selectRaw(self::API_SELECT)
            ->first();

        return $row ? ThreeTCategory::decorate($row) : null;
    }

    /**
     * Sekolah aktif dalam radius titik, terdekat lebih dulu (ST_DWithin + indeks GiST).
     *
     * @return array<int, object>
     */
    public function nearby(float|int|string $lat, float|int|string $lng, int|string $radiusMeters, int $limit, ?string $level): array
    {
        $bindings = [$lng, $lat, $radiusMeters];
        $levelSql = '';
        if (filled($level)) {
            $levelSql = 'AND s.educational_level = ?';
            $bindings[] = $level;
        }
        $bindings[] = $limit;

        return DB::select(
            'SELECT s.id, s.npsn, s.name, s.educational_level,
                    ST_Y(s.coordinates::geometry) AS lat, ST_X(s.coordinates::geometry) AS lng,
                    ST_Distance(s.coordinates, q.p) AS dist_meters
             FROM schools s
             CROSS JOIN (SELECT '.self::POINT.' AS p) q
             WHERE s.is_active
               AND ST_DWithin(s.coordinates, q.p, CAST(? AS float8))
               '.$levelSql.'
             ORDER BY dist_meters
             LIMIT ?',
            $bindings
        );
    }

    /**
     * FeatureCollection sekolah aktif untuk peta.
     *
     * @param  string|null  $bbox  "minLng,minLat,maxLng,maxLat"
     * @return array{type: string, features: array<int, mixed>}
     */
    public function geoJson(?string $bbox, ?string $level, int $limit): array
    {
        $where = ['is_active'];
        $bindings = [];
        if (filled($bbox)) {
            [$minX, $minY, $maxX, $maxY] = array_map('floatval', explode(',', $bbox));
            // geography && geography memakai indeks GiST
            $where[] = 'coordinates && ST_MakeEnvelope(CAST(? AS float8), CAST(? AS float8), CAST(? AS float8), CAST(? AS float8), 4326)::geography';
            array_push($bindings, $minX, $minY, $maxX, $maxY);
        }
        if (filled($level)) {
            $where[] = 'educational_level = ?';
            $bindings[] = $level;
        }
        $bindings[] = $limit;

        $row = DB::selectOne(
            "SELECT COALESCE(jsonb_agg(jsonb_build_object(
                        'type', 'Feature',
                        'geometry', ST_AsGeoJSON(s.coordinates::geometry)::jsonb,
                        'properties', jsonb_build_object(
                            'id', s.id, 'npsn', s.npsn, 'name', s.name,
                            'level', s.educational_level, 'is_3t', r.is_3t,
                            'is_tertinggal', r.is_tertinggal, 'is_terdepan', r.is_terdepan,
                            'is_terluar', r.is_terluar))), '[]'::jsonb) AS features
             FROM (SELECT id, region_id, npsn, name, educational_level, coordinates
                   FROM schools WHERE ".implode(' AND ', $where).' LIMIT ?) s
             JOIN regions r ON r.id = s.region_id',
            $bindings
        );

        return [
            'type' => 'FeatureCollection',
            'features' => json_decode($row->features, true),
        ];
    }
}
