<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ThreeTCategory;
use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\Schools\SchoolImporter;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * PBI-01 / FR-01: profil sekolah dan koordinat master.
 * Baca = publik, tulis = Admin (lihat routes/domain1.php).
 */
class SchoolController extends Controller
{
    use ApiResponses;

    /** Jenjang sesuai FR-17 (filter SD/SMP/SMA). */
    public const LEVELS = ['SD', 'SMP', 'SMA'];

    private const ACCREDITATIONS = ['A', 'B', 'C'];

    /** Kolom wilayah 3T ikut dibaca setiap kali sekolah ditampilkan. */
    private const SELECT = 's.id, s.npsn, s.name, s.educational_level, s.address, s.accreditation,
        s.contact_phone, s.contact_email, s.is_active,
        ST_Y(s.coordinates::geometry) AS lat, ST_X(s.coordinates::geometry) AS lng,
        r.id AS region_id, r.code AS region_code, r.name AS region_name, r.is_3t_tier,
        r.is_3t, r.is_tertinggal, r.is_terdepan, r.is_terluar';

    /** Titik dari (lng, lat) - urutan bind: lng, lat. */
    private const POINT = 'ST_SetSRID(ST_MakePoint(CAST(? AS float8), CAST(? AS float8)), 4326)::geography';

    // ------------------------------------------------------------------ READ

    /** GET /schools - cari (NPSN/nama), filter jenjang/wilayah/3T/kategori, paginasi (FR-17). */
    public function index(Request $request): JsonResponse
    {
        $v = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', Rule::in(self::LEVELS)],
            'region_id' => ['nullable', 'integer', 'min:1'],
            'region_code' => ['nullable', 'string', 'max:20'],
            'is_3t' => ['nullable', 'in:true,false,1,0'],
            'category' => ['nullable', Rule::in(ThreeTCategory::values())],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = DB::table('schools as s')
            ->join('regions as r', 'r.id', '=', 's.region_id')
            ->where('s.is_active', true);

        if (filled($v['q'] ?? null)) {
            $q = $v['q'];
            $like = '%' . $this->escapeLike($q) . '%';
            $query->where(fn ($w) => $w->where('s.npsn', $q)->orWhere('s.name', 'ilike', $like));
        }
        if (filled($v['level'] ?? null)) {
            $query->where('s.educational_level', $v['level']);
        }
        if (filled($v['region_id'] ?? null)) {
            $query->where('s.region_id', (int) $v['region_id']);
        }
        if (filled($v['region_code'] ?? null)) {
            // kode provinsi adalah awalan kode kabupaten: 53 -> 53.07
            $query->where('r.code', 'like', $this->escapeLike($v['region_code']) . '%');
        }
        if (filled($v['is_3t'] ?? null)) {
            $query->where('r.is_3t', filter_var($v['is_3t'], FILTER_VALIDATE_BOOLEAN));
        }
        if (filled($v['category'] ?? null)) {
            $query->where('r.' . ThreeTCategory::from($v['category'])->column(), true);
        }

        $page = $query->selectRaw(self::SELECT)
            ->orderBy('s.name')->orderBy('s.id')
            ->paginate((int) ($v['per_page'] ?? 20));

        $items = array_map(fn (object $row): object => ThreeTCategory::decorate($row), $page->items());

        return $this->ok($items, $this->pageMeta($page));
    }

    /** GET /schools/{id} */
    public function show(int $id): JsonResponse
    {
        $row = $this->findActive($id);

        return $row ? $this->ok(ThreeTCategory::decorate($row)) : $this->fail(404, 'Sekolah tidak ditemukan.');
    }

    /** GET /schools/nearby - sekolah dalam radius titik (ST_DWithin + indeks GiST). */
    public function nearby(Request $request): JsonResponse
    {
        $v = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius_m' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'level' => ['nullable', Rule::in(self::LEVELS)],
        ]);

        $bindings = [$v['lng'], $v['lat'], $v['radius_m'] ?? 15000];
        $levelSql = '';
        if (filled($v['level'] ?? null)) {
            $levelSql = 'AND s.educational_level = ?';
            $bindings[] = $v['level'];
        }
        $bindings[] = (int) ($v['limit'] ?? 20);

        $rows = DB::select(
            'SELECT s.id, s.npsn, s.name, s.educational_level,
                    ST_Y(s.coordinates::geometry) AS lat, ST_X(s.coordinates::geometry) AS lng,
                    ST_Distance(s.coordinates, q.p) AS dist_meters
             FROM schools s
             CROSS JOIN (SELECT ' . self::POINT . ' AS p) q
             WHERE s.is_active
               AND ST_DWithin(s.coordinates, q.p, CAST(? AS float8))
               ' . $levelSql . '
             ORDER BY dist_meters
             LIMIT ?',
            $bindings
        );

        return $this->ok($rows);
    }

    /** GET /schools/geojson - FeatureCollection untuk peta (respons GeoJSON murni, tanpa envelope data). */
    public function geojson(Request $request): JsonResponse
    {
        $v = $request->validate([
            'bbox' => ['nullable', 'string', 'regex:/^-?\d+(\.\d+)?(,-?\d+(\.\d+)?){3}$/'],
            'level' => ['nullable', Rule::in(self::LEVELS)],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50000'],
        ]);

        $where = ['is_active'];
        $bindings = [];
        if (filled($v['bbox'] ?? null)) {
            [$minX, $minY, $maxX, $maxY] = array_map('floatval', explode(',', $v['bbox']));
            // geography && geography memakai indeks GiST
            $where[] = 'coordinates && ST_MakeEnvelope(CAST(? AS float8), CAST(? AS float8), CAST(? AS float8), CAST(? AS float8), 4326)::geography';
            array_push($bindings, $minX, $minY, $maxX, $maxY);
        }
        if (filled($v['level'] ?? null)) {
            $where[] = 'educational_level = ?';
            $bindings[] = $v['level'];
        }
        $bindings[] = (int) ($v['limit'] ?? 10000);

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
                   FROM schools WHERE " . implode(' AND ', $where) . ' LIMIT ?) s
             JOIN regions r ON r.id = s.region_id',
            $bindings
        );

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => json_decode($row->features, true),
        ]);
    }

    // ----------------------------------------------------------------- WRITE

    /** POST /schools - daftarkan sekolah (FR-01). */
    public function store(Request $request, SchoolImporter $importer): JsonResponse
    {
        $data = $request->validate($importer->profileRules(['required', Rule::in(self::LEVELS)]));

        $regionId = $importer->regionIdFor($data);
        if ($regionId === null) {
            return $this->fail(422, 'Koordinat tidak berada di dalam batas wilayah mana pun.', [
                'region_id' => ['Kirim region_id secara manual atau impor batas wilayah terlebih dahulu.'],
            ]);
        }

        try {
            $id = DB::transaction(fn () => $this->insertSchool($data, $regionId)->id);
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23505') {
                return $this->fail(409, 'NPSN sudah terdaftar (mungkin berstatus nonaktif).');
            }
            throw $e;
        }

        return $this->ok(ThreeTCategory::decorate($this->findActive($id)), null, 201);
    }

    /**
     * PUT /schools/{id} - ubah kontak dan akreditasi (Tabel 16: U = perubahan kontak/akreditasi).
     * Koreksi nama/koordinat/wilayah dilakukan lewat impor (upsert berdasarkan NPSN).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'accreditation' => ['sometimes', 'nullable', Rule::in(self::ACCREDITATIONS)],
            'contact_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'contact_email' => ['sometimes', 'nullable', 'email', 'max:150'],
            // field di bawah ini tidak boleh diubah lewat endpoint ini
            'npsn' => ['prohibited'],
            'name' => ['prohibited'],
            'lat' => ['prohibited'],
            'lng' => ['prohibited'],
            'region_id' => ['prohibited'],
        ]);

        if ($data === []) {
            return $this->fail(422, 'Tidak ada field yang diubah (accreditation, contact_phone, contact_email).');
        }

        $affected = School::query()->active()->where('id', $id)->update($data);

        return $affected === 0
            ? $this->fail(404, 'Sekolah tidak ditemukan.')
            : $this->ok(ThreeTCategory::decorate($this->findActive($id)));
    }

    /** DELETE /schools/{id} - soft delete: is_active = false (FR-01). */
    public function destroy(int $id): JsonResponse
    {
        $affected = School::query()->active()->where('id', $id)->update(['is_active' => false]);

        return $affected === 0
            ? $this->fail(404, 'Sekolah tidak ditemukan.')
            : response()->json(null, 204);
    }

    /**
     * POST /schools/import - impor massal, upsert berdasarkan NPSN (FR-01, TR-08).
     * Logika penulisan ada di SchoolImporter agar dipakai bersama sinkronisasi (schools:sync).
     */
    public function import(Request $request, SchoolImporter $importer): JsonResponse
    {
        $request->validate(['schools' => ['required', 'array', 'min:1', 'max:1000']]);

        return $this->ok($importer->import($request->input('schools')));
    }

    // --------------------------------------------------------------- HELPERS

    private function findActive(int $id): ?object
    {
        return DB::table('schools as s')
            ->join('regions as r', 'r.id', '=', 's.region_id')
            ->where('s.id', $id)->where('s.is_active', true)
            ->selectRaw(self::SELECT)
            ->first();
    }

    private function insertSchool(array $d, int $regionId): object
    {
        return DB::selectOne(
            'INSERT INTO schools (region_id, npsn, name, educational_level, address, coordinates,
                                  accreditation, contact_phone, contact_email)
             VALUES (?, ?, ?, ?, ?, ' . self::POINT . ', ?, ?, ?)
             RETURNING id',
            [
                $regionId, (string) $d['npsn'], $d['name'], $d['educational_level'], $d['address'] ?? null,
                $d['lng'], $d['lat'],
                $d['accreditation'] ?? null, $d['contact_phone'] ?? null, $d['contact_email'] ?? null,
            ]
        );
    }
}
