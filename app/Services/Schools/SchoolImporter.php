<?php

namespace App\Services\Schools;

use App\Http\Controllers\Api\V1\SchoolController;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Logika penulisan sekolah yang dipakai bersama oleh endpoint impor API dan
 * sinkronisasi sumber eksternal (CSV / Dapodik). Upsert berdasarkan NPSN (FR-01, TR-08).
 */
class SchoolImporter
{
    /** Pemetaan jenjang dari sumber eksternal ke 3 jenjang sistem. */
    private const LEVEL_MAP = [
        'SD' => 'SD', 'MI' => 'SD', 'SDLB' => 'SD',
        'SMP' => 'SMP', 'MTS' => 'SMP', 'SMPLB' => 'SMP',
        'SMA' => 'SMA', 'SMK' => 'SMA', 'MA' => 'SMA', 'SMLB' => 'SMA',
    ];

    private const ACCREDITATIONS = ['A', 'B', 'C'];

    /** Titik dari (lng, lat) - urutan bind: lng, lat. */
    private const POINT = 'ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography';

    /**
     * Impor banyak baris. Tiap baris diproses mandiri: baris gagal dilaporkan tanpa menghentikan baris lain.
     *
     * @param  array<int, mixed>  $rows
     * @param  int  $rowOffset  nomor baris awal untuk pelaporan error (dipakai sinkronisasi per batch)
     * @return array{created: int, updated: int, failed: int, errors: list<array<string, mixed>>}
     */
    public function import(array $rows, int $rowOffset = 0): array
    {
        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $i => $raw) {
            $rowNo = $rowOffset + $i + 1;
            if (! is_array($raw)) {
                $errors[] = ['row' => $rowNo, 'npsn' => null, 'message' => 'Baris harus berupa objek.'];
                continue;
            }

            $level = self::LEVEL_MAP[strtoupper(trim((string) ($raw['educational_level'] ?? '')))] ?? null;
            if ($level === null) {
                $errors[] = ['row' => $rowNo, 'npsn' => $raw['npsn'] ?? null,
                    'message' => 'Jenjang tidak didukung (hanya SD/SMP/SMA dan padanannya MI/MTs/MA/SMK).'];
                continue;
            }
            $raw['educational_level'] = $level;

            $validator = Validator::make($raw, $this->profileRules(['required', Rule::in(SchoolController::LEVELS)]));
            if ($validator->fails()) {
                $errors[] = ['row' => $rowNo, 'npsn' => $raw['npsn'] ?? null,
                    'message' => implode('; ', $validator->errors()->all())];
                continue;
            }
            $data = $validator->validated();

            $regionId = $this->regionIdFor($data);
            if ($regionId === null) {
                $errors[] = ['row' => $rowNo, 'npsn' => $data['npsn'],
                    'message' => 'Koordinat tidak berada di dalam batas wilayah mana pun dan region_id tidak dikirim.'];
                continue;
            }

            try {
                $inserted = DB::transaction(fn () => $this->upsertSchool($data, $regionId));
                $inserted ? $created++ : $updated++;
            } catch (QueryException $e) {
                $errors[] = ['row' => $rowNo, 'npsn' => $data['npsn'],
                    'message' => 'Gagal menyimpan (SQLSTATE '.$e->getCode().').'];
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'failed' => count($errors),
            'errors' => $errors,
        ];
    }

    /** @return array<string, array<int, mixed>> */
    public function profileRules(array $levelRule): array
    {
        return [
            'npsn' => ['required', 'regex:/^[0-9]{8}$/'],
            'name' => ['required', 'string', 'max:200'],
            'educational_level' => $levelRule,
            'address' => ['nullable', 'string', 'max:1000'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'accreditation' => ['nullable', Rule::in(self::ACCREDITATIONS)],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'region_code' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Tentukan wilayah sekolah: region_id, lalu region_code (kode wilayah sumber),
     * lalu titik koordinat terhadap batas wilayah.
     *
     * @param  array<string, mixed>  $data
     */
    public function regionIdFor(array $data): ?int
    {
        if (isset($data['region_id'])) {
            return (int) $data['region_id'];
        }

        if (filled($data['region_code'] ?? null)) {
            $id = DB::table('regions')->where('code', $data['region_code'])->value('id');
            if ($id !== null) {
                return (int) $id;
            }
        }

        return $this->regionIdForPoint((float) $data['lat'], (float) $data['lng']);
    }

    /** Wilayah paling spesifik (kec > kab > prov) yang memuat titik. */
    public function regionIdForPoint(float $lat, float $lng): ?int
    {
        $row = DB::selectOne(
            'SELECT id FROM regions
             WHERE boundary_polygon IS NOT NULL AND ST_Covers(boundary_polygon, '.self::POINT.')
             ORDER BY CASE level WHEN \'kec\' THEN 1 WHEN \'kab\' THEN 2 ELSE 3 END, id
             LIMIT 1',
            [$lng, $lat]
        );

        return $row ? (int) $row->id : null;
    }

    /** @return bool true bila baris baru dibuat, false bila memperbarui yang sudah ada. */
    private function upsertSchool(array $d, int $regionId): bool
    {
        $row = DB::selectOne(
            'INSERT INTO schools (region_id, npsn, name, educational_level, address, coordinates,
                                  accreditation, contact_phone, contact_email)
             VALUES (?, ?, ?, ?, ?, '.self::POINT.', ?, ?, ?)
             ON CONFLICT (npsn) DO UPDATE SET
                 region_id = EXCLUDED.region_id,
                 name = EXCLUDED.name,
                 educational_level = EXCLUDED.educational_level,
                 address = EXCLUDED.address,
                 coordinates = EXCLUDED.coordinates,
                 accreditation = COALESCE(EXCLUDED.accreditation, schools.accreditation),
                 contact_phone = COALESCE(EXCLUDED.contact_phone, schools.contact_phone),
                 contact_email = COALESCE(EXCLUDED.contact_email, schools.contact_email)
             RETURNING (xmax = 0) AS inserted',
            [
                $regionId, (string) $d['npsn'], $d['name'], $d['educational_level'], $d['address'] ?? null,
                $d['lng'], $d['lat'],
                $d['accreditation'] ?? null, $d['contact_phone'] ?? null, $d['contact_email'] ?? null,
            ]
        );

        return (bool) $row->inserted;
    }
}
