<?php

namespace App\Services\Regions;

use App\Concerns\EscapesLikeWildcards;
use App\Enums\ThreeTCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Query baca wilayah administrasi (PBI-02 / FR-02): daftar, detail + poligon, GeoJSON, dan pencarian titik.
 * Semua hasil sudah didekorasi kategori 3T (ThreeTCategory::decorate).
 */
class RegionQueryService
{
    use EscapesLikeWildcards;

    /** Kolom 3T yang ikut dibaca di setiap respons wilayah. */
    private const THREE_T_SELECT = 'is_3t_tier, is_3t, is_tertinggal, is_terdepan, is_terluar';

    /**
     * Daftar wilayah tanpa geometri, urut kode.
     *
     * @param  array{q?: ?string, level?: ?string, code_prefix?: ?string, is_3t?: ?bool, category?: ?string}  $filters
     */
    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = DB::table('regions');
        if (filled($filters['q'] ?? null)) {
            $query->where('name', 'ilike', '%'.$this->escapeLike($filters['q']).'%');
        }
        if (filled($filters['level'] ?? null)) {
            $query->where('level', $filters['level']);
        }
        if (filled($filters['code_prefix'] ?? null)) {
            $query->where('code', 'like', $this->escapeLike($filters['code_prefix']).'%');
        }
        if (isset($filters['is_3t'])) {
            $query->where('is_3t', $filters['is_3t']);
        }
        if (filled($filters['category'] ?? null)) {
            $query->where(ThreeTCategory::from($filters['category'])->column(), true);
        }

        return $query
            ->selectRaw('id, code, name, level, (boundary_polygon IS NOT NULL) AS has_boundary, '.self::THREE_T_SELECT)
            ->orderBy('code')
            ->paginate($perPage)
            ->through(fn (object $row): object => ThreeTCategory::decorate($row));
    }

    /** Detail wilayah beserta poligon (GeoJSON geometry), atau null. */
    public function findWithGeometry(int $id): ?object
    {
        $row = DB::table('regions')->where('id', $id)
            ->selectRaw('id, code, name, level, ST_AsGeoJSON(boundary_polygon::geometry) AS geometry, '.self::THREE_T_SELECT)
            ->first();

        if (! $row) {
            return null;
        }
        $row->geometry = $row->geometry ? json_decode($row->geometry) : null;

        return ThreeTCategory::decorate($row);
    }

    /** Ringkasan wilayah tanpa geometri, atau null. */
    public function findSummary(int $id): ?object
    {
        $row = DB::table('regions')->where('id', $id)
            ->selectRaw('id, code, name, level, '.self::THREE_T_SELECT)
            ->first();

        return $row ? ThreeTCategory::decorate($row) : null;
    }

    /**
     * FeatureCollection wilayah untuk peta/choropleth.
     * $simplify (derajat; 0 = tanpa penyederhanaan) menjaga payload tetap ringan.
     *
     * @param  array{level?: ?string, code_prefix?: ?string, is_3t?: ?bool, category?: ?string}  $filters
     * @return array{type: string, features: array<int, mixed>}
     */
    public function geoJson(array $filters, float $simplify): array
    {
        $where = ['boundary_polygon IS NOT NULL'];
        $bindings = [$simplify, $simplify];

        if (filled($filters['level'] ?? null)) {
            $where[] = 'level = ?';
            $bindings[] = $filters['level'];
        }
        if (filled($filters['code_prefix'] ?? null)) {
            $where[] = 'code LIKE ?';
            $bindings[] = $this->escapeLike($filters['code_prefix']).'%';
        }
        if (isset($filters['is_3t'])) {
            $where[] = $filters['is_3t'] ? 'is_3t' : 'NOT is_3t';
        }
        if (filled($filters['category'] ?? null)) {
            $where[] = ThreeTCategory::from($filters['category'])->column();
        }

        $row = DB::selectOne(
            "SELECT COALESCE(jsonb_agg(jsonb_build_object(
                        'type', 'Feature',
                        'geometry', ST_AsGeoJSON(CASE WHEN CAST(? AS float8) > 0
                                                      THEN ST_SimplifyPreserveTopology(boundary_polygon::geometry, CAST(? AS float8))
                                                      ELSE boundary_polygon::geometry END)::jsonb,
                        'properties', jsonb_build_object(
                            'id', id, 'code', code, 'name', name, 'level', level, 'is_3t_tier', is_3t_tier,
                            'is_3t', is_3t, 'is_tertinggal', is_tertinggal,
                            'is_terdepan', is_terdepan, 'is_terluar', is_terluar)
                    )), '[]'::jsonb) AS features
             FROM regions WHERE ".implode(' AND ', $where),
            $bindings
        );

        return [
            'type' => 'FeatureCollection',
            'features' => json_decode($row->features, true),
        ];
    }

    /**
     * Wilayah yang memuat titik, paling spesifik (kec > kab > prov) lebih dulu.
     *
     * @return array<int, object>
     */
    public function containingPoint(float|int|string $lat, float|int|string $lng): array
    {
        $rows = DB::select(
            'SELECT id, code, name, level, '.self::THREE_T_SELECT."
             FROM regions
             WHERE boundary_polygon IS NOT NULL
               AND ST_Covers(boundary_polygon,
                             ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography)
             ORDER BY CASE level WHEN 'kec' THEN 1 WHEN 'kab' THEN 2 ELSE 3 END, id",
            [$lng, $lat]
        );

        return array_map(fn (object $row): object => ThreeTCategory::decorate($row), $rows);
    }
}
