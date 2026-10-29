<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * PBI-02 / FR-02: batas wilayah administrasi dan penanda 3T.
 * Operasi sesuai Tabel 16: C = impor GeoJSON, R = poligon, U = status 3T, D = hapus batas.
 */
class RegionController extends Controller
{
    use ApiResponses;

    public const LEVELS = ['prov', 'kab', 'kec'];

    // ------------------------------------------------------------------ READ

    /** GET /regions - daftar wilayah tanpa geometri. */
    public function index(Request $request): JsonResponse
    {
        $v = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', Rule::in(self::LEVELS)],
            'code_prefix' => ['nullable', 'string', 'max:20'],
            'is_3t' => ['nullable', 'in:true,false,1,0'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $query = DB::table('regions');
        if (filled($v['q'] ?? null)) {
            $query->where('name', 'ilike', '%' . $this->escapeLike($v['q']) . '%');
        }
        if (filled($v['level'] ?? null)) {
            $query->where('level', $v['level']);
        }
        if (filled($v['code_prefix'] ?? null)) {
            $query->where('code', 'like', $this->escapeLike($v['code_prefix']) . '%');
        }
        if (filled($v['is_3t'] ?? null)) {
            $query->where('is_3t_tier', filter_var($v['is_3t'], FILTER_VALIDATE_BOOLEAN) ? '>' : '=', 0);
        }

        $page = $query
            ->selectRaw('id, code, name, level, is_3t_tier, (boundary_polygon IS NOT NULL) AS has_boundary')
            ->orderBy('code')
            ->paginate((int) ($v['per_page'] ?? 100));

        return $this->ok($page->items(), $this->pageMeta($page));
    }

    /** GET /regions/{id} - detail + poligon (GeoJSON geometry). */
    public function show(int $id): JsonResponse
    {
        $row = DB::table('regions')->where('id', $id)
            ->selectRaw('id, code, name, level, is_3t_tier, ST_AsGeoJSON(boundary_polygon::geometry) AS geometry')
            ->first();

        if (! $row) {
            return $this->fail(404, 'Wilayah tidak ditemukan.');
        }
        $row->geometry = $row->geometry ? json_decode($row->geometry) : null;

        return $this->ok($row);
    }

    /**
     * GET /regions/geojson - FeatureCollection untuk peta/choropleth (respons GeoJSON murni).
     * `simplify` (derajat, default 0.001; 0 = tanpa penyederhanaan) menjaga payload tetap ringan.
     */
    public function geojson(Request $request): JsonResponse
    {
        $v = $request->validate([
            'level' => ['nullable', Rule::in(self::LEVELS)],
            'code_prefix' => ['nullable', 'string', 'max:20'],
            'is_3t' => ['nullable', 'in:true,false,1,0'],
            'simplify' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ]);

        $simplify = (float) ($v['simplify'] ?? 0.001);
        $where = ['boundary_polygon IS NOT NULL'];
        $bindings = [$simplify, $simplify];

        if (filled($v['level'] ?? null)) {
            $where[] = 'level = ?';
            $bindings[] = $v['level'];
        }
        if (filled($v['code_prefix'] ?? null)) {
            $where[] = 'code LIKE ?';
            $bindings[] = $this->escapeLike($v['code_prefix']) . '%';
        }
        if (filled($v['is_3t'] ?? null)) {
            $where[] = filter_var($v['is_3t'], FILTER_VALIDATE_BOOLEAN) ? 'is_3t_tier > 0' : 'is_3t_tier = 0';
        }

        $row = DB::selectOne(
            "SELECT COALESCE(jsonb_agg(jsonb_build_object(
                        'type', 'Feature',
                        'geometry', ST_AsGeoJSON(CASE WHEN CAST(? AS float8) > 0
                                                      THEN ST_SimplifyPreserveTopology(boundary_polygon::geometry, CAST(? AS float8))
                                                      ELSE boundary_polygon::geometry END)::jsonb,
                        'properties', jsonb_build_object(
                            'id', id, 'code', code, 'name', name, 'level', level, 'is_3t_tier', is_3t_tier)
                    )), '[]'::jsonb) AS features
             FROM regions WHERE " . implode(' AND ', $where),
            $bindings
        );

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => json_decode($row->features, true),
        ]);
    }

    /** GET /regions/lookup?lat=&lng= - wilayah yang memuat titik (paling spesifik lebih dulu). */
    public function lookup(Request $request): JsonResponse
    {
        $v = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $rows = DB::select(
            "SELECT id, code, name, level, is_3t_tier
             FROM regions
             WHERE boundary_polygon IS NOT NULL
               AND ST_Covers(boundary_polygon,
                             ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography)
             ORDER BY CASE level WHEN 'kec' THEN 1 WHEN 'kab' THEN 2 ELSE 3 END, id",
            [$v['lng'], $v['lat']]
        );

        return $this->ok($rows);
    }

    // ----------------------------------------------------------------- WRITE

    /**
     * POST /regions/import - impor GeoJSON FeatureCollection (FR-02), upsert berdasarkan code.
     * properties: { code, name, level: prov|kab|kec, is_3t_tier?: 0|1|2 }.
     * Polygon dinormalkan menjadi MultiPolygon dan diperbaiki (ST_MakeValid) bila perlu.
     * Tiap fitur diproses mandiri; yang gagal dilaporkan tanpa menghentikan yang lain.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:FeatureCollection'],
            'features' => ['required', 'array', 'min:1', 'max:500'],
        ]);

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($request->input('features') as $i => $feature) {
            $rowNo = $i + 1;
            $code = is_array($feature) ? ($feature['properties']['code'] ?? null) : null;

            $validator = Validator::make(is_array($feature) ? $feature : [], [
                'properties.code' => ['required', 'string', 'max:20'],
                'properties.name' => ['required', 'string', 'max:150'],
                'properties.level' => ['required', Rule::in(self::LEVELS)],
                'properties.is_3t_tier' => ['nullable', 'integer', 'in:0,1,2'],
                'geometry.type' => ['required', 'in:Polygon,MultiPolygon'],
                'geometry.coordinates' => ['required', 'array'],
            ]);
            if ($validator->fails()) {
                $errors[] = ['row' => $rowNo, 'code' => $code, 'message' => implode('; ', $validator->errors()->all())];
                continue;
            }

            $p = $feature['properties'];
            $tier = $p['is_3t_tier'] ?? null;

            try {
                $result = DB::transaction(fn () => DB::select(
                    "WITH g AS (
                         SELECT ST_Multi(ST_CollectionExtract(
                                    ST_MakeValid(ST_SetSRID(ST_GeomFromGeoJSON(CAST(? AS text)), 4326)), 3)) AS geom
                     )
                     INSERT INTO regions (code, name, level, boundary_polygon, is_3t_tier)
                     SELECT ?, ?, ?, g.geom::geography, COALESCE(CAST(? AS smallint), 0)
                     FROM g WHERE NOT ST_IsEmpty(g.geom)
                     ON CONFLICT (code) DO UPDATE SET
                         name = EXCLUDED.name,
                         level = EXCLUDED.level,
                         boundary_polygon = EXCLUDED.boundary_polygon,
                         is_3t_tier = COALESCE(CAST(? AS smallint), regions.is_3t_tier)
                     RETURNING (xmax = 0) AS inserted",
                    [json_encode($feature['geometry']), $p['code'], $p['name'], $p['level'], $tier, $tier]
                ));
            } catch (QueryException $e) {
                $errors[] = ['row' => $rowNo, 'code' => $p['code'],
                    'message' => 'Geometri tidak dapat diproses (SQLSTATE ' . $e->getCode() . ').'];
                continue;
            }

            if ($result === []) {
                $errors[] = ['row' => $rowNo, 'code' => $p['code'], 'message' => 'Geometri kosong atau bukan poligon yang valid.'];
                continue;
            }
            $result[0]->inserted ? $created++ : $updated++;
        }

        return $this->ok([
            'created' => $created,
            'updated' => $updated,
            'failed' => count($errors),
            'errors' => $errors,
        ]);
    }

    /** PATCH /regions/{id}/tier - perbarui status prioritas 3T (FR-02). */
    public function updateTier(Request $request, int $id): JsonResponse
    {
        $v = $request->validate(['is_3t_tier' => ['required', 'integer', 'in:0,1,2']]);

        $affected = Region::query()->where('id', $id)->update(['is_3t_tier' => $v['is_3t_tier']]);
        if ($affected === 0) {
            return $this->fail(404, 'Wilayah tidak ditemukan.');
        }

        return $this->ok(DB::table('regions')->where('id', $id)
            ->select('id', 'code', 'name', 'level', 'is_3t_tier')->first());
    }

    /** DELETE /regions/{id} - hapus batas wilayah; ditolak 409 bila masih dipakai sekolah. */
    public function destroy(int $id): JsonResponse
    {
        try {
            $affected = DB::transaction(fn () => Region::query()->where('id', $id)->delete());
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23503') {
                return $this->fail(409, 'Wilayah masih dipakai oleh sekolah dan tidak dapat dihapus.');
            }
            throw $e;
        }

        return $affected === 0
            ? $this->fail(404, 'Wilayah tidak ditemukan.')
            : response()->json(null, 204);
    }
}
