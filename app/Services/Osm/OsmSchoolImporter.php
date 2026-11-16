<?php

namespace App\Services\Osm;

use App\Services\Schools\SchoolImporter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Menyimpan sekolah dari OpenStreetMap. Kunci upsert adalah osm_id (mis. "n123456"),
 * karena sebagian besar sekolah OSM belum punya NPSN. Wilayah ditentukan dari titik koordinat.
 */
class OsmSchoolImporter
{
    public function __construct(
        private OsmSchoolMapper $mapper,
        private SchoolImporter $schools,
    ) {}

    /**
     * @param  list<array<string, mixed>>  $elements  elemen Overpass
     * @return array{created: int, updated: int, failed: int, skipped: array<string, int>}
     */
    public function import(array $elements): array
    {
        $created = 0;
        $updated = 0;
        $failed = 0;
        $skipped = [];

        foreach ($elements as $element) {
            ['record' => $record, 'reason' => $reason] = $this->mapper->toRecord($element);
            if ($record === null) {
                $skipped[$reason] = ($skipped[$reason] ?? 0) + 1;
                continue;
            }

            $regionId = $this->schools->regionIdForPoint($record['lat'], $record['lng']);
            if ($regionId === null) {
                $skipped['di_luar_wilayah'] = ($skipped['di_luar_wilayah'] ?? 0) + 1;
                continue;
            }

            try {
                $inserted = DB::transaction(fn () => $this->upsert($record, $regionId));
                $inserted ? $created++ : $updated++;
            } catch (QueryException) {
                $failed++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'failed' => $failed, 'skipped' => $skipped];
    }

    /**
     * @param  array<string, mixed>  $r
     * @return bool true bila baris baru, false bila memperbarui yang sudah ada.
     */
    private function upsert(array $r, int $regionId): bool
    {
        $row = DB::selectOne(
            "INSERT INTO schools (region_id, osm_id, source, name, educational_level, address, coordinates,
                                  contact_phone, contact_email)
             VALUES (?, ?, 'osm', ?, ?, ?, ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography, ?, ?)
             ON CONFLICT (osm_id) DO UPDATE SET
                 region_id = EXCLUDED.region_id,
                 name = EXCLUDED.name,
                 educational_level = EXCLUDED.educational_level,
                 address = COALESCE(EXCLUDED.address, schools.address),
                 coordinates = EXCLUDED.coordinates,
                 contact_phone = COALESCE(EXCLUDED.contact_phone, schools.contact_phone),
                 contact_email = COALESCE(EXCLUDED.contact_email, schools.contact_email)
             RETURNING (xmax = 0) AS inserted",
            [
                $regionId, $r['osm_id'], $r['name'], $r['educational_level'], $r['address'],
                $r['lng'], $r['lat'], $r['contact_phone'], $r['contact_email'],
            ]
        );

        return (bool) $row->inserted;
    }
}
