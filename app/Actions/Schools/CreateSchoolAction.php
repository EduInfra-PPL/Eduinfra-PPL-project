<?php

namespace App\Actions\Schools;

use App\Services\Schools\SchoolImporter;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Mendaftarkan sekolah baru secara manual (FR-01). Wilayah diambil dari region_id,
 * atau dari titik koordinat bila region_id tidak dikirim.
 */
class CreateSchoolAction
{
    public function __construct(private SchoolImporter $importer) {}

    /** @return int id sekolah yang dibuat */
    public function execute(array $data): int
    {
        $regionId = $this->importer->regionIdFor($data);
        if ($regionId === null) {
            throw new RuntimeException('Koordinat tidak berada di dalam batas wilayah mana pun.');
        }

        return DB::transaction(fn () => (int) DB::selectOne(
            'INSERT INTO schools (region_id, npsn, name, educational_level, address, coordinates,
                                  accreditation, contact_phone, contact_email, source)
             VALUES (?, ?, ?, ?, ?, ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography, ?, ?, ?, \'manual\')
             RETURNING id',
            [
                $regionId, (string) $data['npsn'], $data['name'], $data['educational_level'], $data['address'] ?? null,
                $data['lng'], $data['lat'],
                $data['accreditation'] ?? null, $data['contact_phone'] ?? null, $data['contact_email'] ?? null,
            ]
        )->id);
    }
}
