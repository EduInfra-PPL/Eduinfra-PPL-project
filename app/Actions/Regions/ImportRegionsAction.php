<?php

namespace App\Actions\Regions;

use App\Models\Region;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Impor GeoJSON FeatureCollection batas wilayah (FR-02), upsert berdasarkan code.
 *
 * properties: { code, name, level: prov|kab|kec, is_3t_tier?: 0|1|2,
 *               is_tertinggal?, is_terdepan?, is_terluar?: bool }.
 * Tier > 0 tanpa is_tertinggal eksplisit dianggap daerah tertinggal (Tier-1 = Perpres 63/2020).
 * Polygon dinormalkan menjadi MultiPolygon dan diperbaiki (ST_MakeValid) bila perlu.
 * Tiap fitur diproses mandiri; yang gagal dilaporkan tanpa menghentikan yang lain.
 */
class ImportRegionsAction
{
    /**
     * @param  array<int, mixed>  $features
     * @return array{created: int, updated: int, failed: int, errors: array<int, array{row: int, code: mixed, message: string}>}
     */
    public function execute(array $features): array
    {
        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($features as $i => $feature) {
            $rowNo = $i + 1;
            $code = is_array($feature) ? ($feature['properties']['code'] ?? null) : null;

            $validator = Validator::make(is_array($feature) ? $feature : [], $this->featureRules());
            if ($validator->fails()) {
                $errors[] = ['row' => $rowNo, 'code' => $code, 'message' => implode('; ', $validator->errors()->all())];

                continue;
            }

            $properties = $feature['properties'];
            try {
                $result = $this->upsert($feature['geometry'], $properties);
            } catch (QueryException $e) {
                $errors[] = ['row' => $rowNo, 'code' => $properties['code'],
                    'message' => 'Geometri tidak dapat diproses (SQLSTATE '.$e->getCode().').'];

                continue;
            }

            if ($result === []) {
                $errors[] = ['row' => $rowNo, 'code' => $properties['code'], 'message' => 'Geometri kosong atau bukan poligon yang valid.'];

                continue;
            }
            $result[0]->inserted ? $created++ : $updated++;
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'failed' => count($errors),
            'errors' => $errors,
        ];
    }

    /** @return array<string, array<int, mixed>> */
    private function featureRules(): array
    {
        return [
            'properties.code' => ['required', 'string', 'max:20'],
            'properties.name' => ['required', 'string', 'max:150'],
            'properties.level' => ['required', Rule::in(Region::LEVELS)],
            'properties.is_3t_tier' => ['nullable', 'integer', 'in:0,1,2'],
            'properties.is_tertinggal' => ['nullable', 'boolean'],
            'properties.is_terdepan' => ['nullable', 'boolean'],
            'properties.is_terluar' => ['nullable', 'boolean'],
            'geometry.type' => ['required', 'in:Polygon,MultiPolygon'],
            'geometry.coordinates' => ['required', 'array'],
        ];
    }

    /**
     * Insert atau perbarui satu wilayah. Hasil kosong berarti geometri kosong setelah dinormalkan.
     *
     * @return array<int, object{inserted: bool}>
     */
    private function upsert(array $geometry, array $p): array
    {
        $tier = $p['is_3t_tier'] ?? null;
        $tertinggal = $p['is_tertinggal'] ?? (($tier ?? 0) > 0 ? true : null);
        $terdepan = $p['is_terdepan'] ?? null;
        $terluar = $p['is_terluar'] ?? null;

        // savepoint per fitur: kegagalan satu fitur tidak membatalkan fitur lain
        return DB::transaction(fn () => DB::select(
            "WITH g AS (
                 SELECT ST_Multi(ST_CollectionExtract(
                            ST_MakeValid(ST_SetSRID(ST_GeomFromGeoJSON(CAST(? AS text)), 4326)), 3)) AS geom
             )
             INSERT INTO regions (code, name, level, boundary_polygon, is_3t_tier,
                                  is_tertinggal, is_terdepan, is_terluar)
             SELECT ?, ?, ?, g.geom::geography, COALESCE(CAST(? AS smallint), 0),
                    COALESCE(CAST(? AS boolean), FALSE), COALESCE(CAST(? AS boolean), FALSE),
                    COALESCE(CAST(? AS boolean), FALSE)
             FROM g WHERE NOT ST_IsEmpty(g.geom)
             ON CONFLICT (code) DO UPDATE SET
                 name = EXCLUDED.name,
                 level = EXCLUDED.level,
                 boundary_polygon = EXCLUDED.boundary_polygon,
                 is_3t_tier = COALESCE(CAST(? AS smallint), regions.is_3t_tier),
                 is_tertinggal = COALESCE(CAST(? AS boolean), regions.is_tertinggal),
                 is_terdepan = COALESCE(CAST(? AS boolean), regions.is_terdepan),
                 is_terluar = COALESCE(CAST(? AS boolean), regions.is_terluar)
             RETURNING (xmax = 0) AS inserted",
            [
                json_encode($geometry), $p['code'], $p['name'], $p['level'], $tier,
                $tertinggal, $terdepan, $terluar,
                $tier, $tertinggal, $terdepan, $terluar,
            ]
        ));
    }
}
