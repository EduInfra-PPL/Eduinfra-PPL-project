<?php

namespace App\Services\ApiIndonesia;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Mengaitkan sekolah API (yang punya NPSN tetapi tanpa koordinat) dengan sekolah OSM yang sudah ada.
 * Pasangan dibuat hanya bila tepat satu sekolah OSM tanpa NPSN cocok: wilayah (kabupaten/kota),
 * jenjang, dan nama yang sudah dinormalkan. Kasus lain dilaporkan dan dilewati.
 */
class NpsnMatcher
{
    /** @var array<string, int> region code => region id */
    private array $regionIds = [];

    public function __construct(private SchoolNameNormalizer $normalizer = new SchoolNameNormalizer) {}

    /**
     * @param  list<array<string, mixed>>  $records  hasil SchoolDirectoryMapper tanpa koordinat
     * @return array{matched: int, updated: int, skipped: array<string, int>}
     */
    public function match(array $records): array
    {
        $matched = 0;
        $updated = 0;
        $skipped = [];

        // kandidat dikelompokkan per kabupaten agar kueri tidak per baris
        $byRegion = [];
        foreach ($records as $record) {
            $byRegion[$record['region_code']][] = $record;
        }

        foreach ($byRegion as $regionCode => $group) {
            $regionId = $this->regionId((string) $regionCode);
            if ($regionId === null) {
                foreach ($group as $_) {
                    $skipped['wilayah_tidak_ditemukan'] = ($skipped['wilayah_tidak_ditemukan'] ?? 0) + 1;
                }
                continue;
            }

            $candidates = $this->candidates($regionId);

            foreach ($group as $record) {
                $key = $record['educational_level'].'|'.$this->normalizer->key((string) $record['name']);
                $ids = $candidates[$key] ?? [];

                if ($ids === []) {
                    $skipped['tanpa_pasangan'] = ($skipped['tanpa_pasangan'] ?? 0) + 1;
                    continue;
                }
                if (count($ids) > 1) {
                    $skipped['pasangan_ganda'] = ($skipped['pasangan_ganda'] ?? 0) + 1;
                    continue;
                }

                $id = $ids[0];
                try {
                    // COALESCE: data sekolah OSM tetap, kecuali kolom yang kosong diisi dari API
                    $changed = DB::update(
                        'UPDATE schools SET npsn = ?,
                             accreditation = COALESCE(?, accreditation),
                             contact_phone = COALESCE(?, contact_phone),
                             contact_email = COALESCE(?, contact_email),
                             address = COALESCE(address, ?)
                         WHERE id = ? AND npsn IS NULL',
                        [
                            $record['npsn'], $record['accreditation'], $record['contact_phone'],
                            $record['contact_email'], $record['address'], $id,
                        ]
                    );
                } catch (QueryException) {
                    // NPSN ini sudah dipakai sekolah lain
                    $skipped['npsn_sudah_dipakai'] = ($skipped['npsn_sudah_dipakai'] ?? 0) + 1;
                    continue;
                }

                if ($changed > 0) {
                    $matched++;
                    $updated++;
                }
                unset($candidates[$key]);
            }
        }

        return ['matched' => $matched, 'updated' => $updated, 'skipped' => $skipped];
    }

    /** @return array<string, list<int>>  kunci (jenjang|nama) => id sekolah OSM tanpa NPSN */
    private function candidates(int $regionId): array
    {
        $rows = DB::table('schools')
            ->where('region_id', $regionId)
            ->where('source', 'osm')
            ->whereNull('npsn')
            ->where('is_active', true)
            ->select(['id', 'name', 'educational_level'])
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $key = $row->educational_level.'|'.$this->normalizer->key((string) $row->name);
            $map[$key][] = (int) $row->id;
        }

        return $map;
    }

    private function regionId(string $code): ?int
    {
        if (! array_key_exists($code, $this->regionIds)) {
            $id = DB::table('regions')->where('code', $code)->value('id');
            $this->regionIds[$code] = $id === null ? null : (int) $id;
        }

        return $this->regionIds[$code];
    }
}
