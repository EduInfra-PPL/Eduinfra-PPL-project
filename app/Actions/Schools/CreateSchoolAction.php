<?php

namespace App\Actions\Schools;

use App\Services\Schools\SchoolImporter;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Mendaftarkan sekolah baru (FR-01). Wilayah diambil dari region_id,
 * atau dari titik koordinat bila region_id tidak dikirim.
 */
class CreateSchoolAction
{
    /** Titik dari (lng, lat) - urutan bind: lng, lat. */
    private const POINT = 'ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography';

    public function __construct(private SchoolImporter $importer) {}

    /**
     * Pendaftaran manual dari halaman admin (source = manual).
     *
     * @return int id sekolah yang dibuat
     */
    public function execute(array $data): int
    {
        $regionId = $this->importer->regionIdFor($data);
        if ($regionId === null) {
            throw new RuntimeException('Koordinat tidak berada di dalam batas wilayah mana pun.');
        }

        return $this->insert($data, $regionId, 'manual');
    }

    /**
     * Simpan satu baris sekolah di wilayah yang sudah ditentukan.
     *
     * @return int id sekolah yang dibuat
     *
     * @throws \Illuminate\Database\QueryException  misalnya 23505 bila NPSN sudah ada
     */
    public function insert(array $data, int $regionId, string $source): int
    {
        return DB::transaction(fn () => (int) DB::selectOne(
            'INSERT INTO schools (region_id, npsn, name, educational_level, address, coordinates,
                                  accreditation, contact_phone, contact_email, source)
             VALUES (?, ?, ?, ?, ?, '.self::POINT.', ?, ?, ?, ?)
             RETURNING id',
            [
                $regionId, (string) $data['npsn'], $data['name'], $data['educational_level'], $data['address'] ?? null,
                $data['lng'], $data['lat'],
                $data['accreditation'] ?? null, $data['contact_phone'] ?? null, $data['contact_email'] ?? null,
                $source,
            ]
        )->id);
    }
}
