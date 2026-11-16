<?php

namespace App\Services\Wilayah;

use Illuminate\Support\Facades\DB;

/**
 * Menyimpan provinsi dan kabupaten/kota (dengan batas GeoJSON) ke tabel regions.
 * Kode memakai format regions.code: provinsi "32", kabupaten/kota "32.01".
 * Kolom 3T tidak diubah di sini.
 */
class RegionBoundaryImporter
{
    /**
     * @param  array{code: string, name: string, level: string}  $region
     * @param  array<string, mixed>  $geometry  GeoJSON geometry (Polygon/MultiPolygon)
     */
    public function upsert(array $region, array $geometry): bool
    {
        $row = DB::selectOne(
            "WITH g AS (
                 SELECT ST_Multi(ST_CollectionExtract(
                            ST_MakeValid(ST_SetSRID(ST_GeomFromGeoJSON(CAST(? AS text)), 4326)), 3)) AS geom
             )
             INSERT INTO regions (code, name, level, boundary_polygon)
             SELECT ?, ?, ?, g.geom::geography FROM g WHERE NOT ST_IsEmpty(g.geom)
             ON CONFLICT (code) DO UPDATE SET
                 name = EXCLUDED.name,
                 level = EXCLUDED.level,
                 boundary_polygon = EXCLUDED.boundary_polygon
             RETURNING (xmax = 0) AS inserted",
            [json_encode($geometry), $region['code'], $region['name'], $region['level']]
        );

        return $row !== null && (bool) $row->inserted;
    }

    /**
     * Ubah kode Kemendagri "3201" menjadi "32.01". Provinsi "32" tetap.
     */
    public static function formatKabCode(string $raw): string
    {
        $digits = preg_replace('/\D/', '', $raw) ?? '';

        return strlen($digits) === 4 ? substr($digits, 0, 2).'.'.substr($digits, 2) : $digits;
    }
}
