<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Schools\CreateSchoolAction;
use App\Actions\Schools\DeactivateSchoolAction;
use App\Actions\Schools\UpdateSchoolContactAction;
use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Schools\ImportSchoolsRequest;
use App\Http\Requests\Api\V1\Schools\NearbySchoolsRequest;
use App\Http\Requests\Api\V1\Schools\SchoolsGeoJsonRequest;
use App\Http\Requests\Api\V1\Schools\SearchSchoolsRequest;
use App\Http\Requests\Api\V1\Schools\StoreSchoolRequest;
use App\Http\Requests\Api\V1\Schools\UpdateSchoolRequest;
use App\Services\Schools\SchoolImporter;
use App\Services\Schools\SchoolQueryService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * PBI-01 / FR-01: profil sekolah dan koordinat master.
 * Baca = publik, tulis = Admin (lihat routes/api.php).
 */
class SchoolController extends Controller
{
    use ApiResponses;

    /** Sumber baris yang didaftarkan lewat API (sama dengan nilai default kolom schools.source). */
    private const API_SOURCE = 'dapodik';

    public function __construct(private SchoolQueryService $schools) {}

    // ------------------------------------------------------------------ READ

    /** GET /schools - cari (NPSN/nama), filter jenjang/wilayah/3T/kategori, paginasi (FR-17). */
    public function index(SearchSchoolsRequest $request): JsonResponse
    {
        $page = $this->schools->paginateActive($request->filters(), $request->perPage());

        return $this->ok($page->items(), $this->pageMeta($page));
    }

    /** GET /schools/{id} */
    public function show(int $id): JsonResponse
    {
        $school = $this->schools->findActive($id);

        return $school ? $this->ok($school) : $this->fail(404, 'Sekolah tidak ditemukan.');
    }

    /** GET /schools/nearby - sekolah dalam radius titik (ST_DWithin + indeks GiST). */
    public function nearby(NearbySchoolsRequest $request): JsonResponse
    {
        $v = $request->validated();

        return $this->ok($this->schools->nearby(
            $v['lat'], $v['lng'], $v['radius_m'] ?? 15000, (int) ($v['limit'] ?? 20), $v['level'] ?? null,
        ));
    }

    /** GET /schools/geojson - FeatureCollection untuk peta (respons GeoJSON murni, tanpa envelope data). */
    public function geojson(SchoolsGeoJsonRequest $request): JsonResponse
    {
        $v = $request->validated();

        return response()->json($this->schools->geoJson($v['bbox'] ?? null, $v['level'] ?? null, (int) ($v['limit'] ?? 10000)));
    }

    // ----------------------------------------------------------------- WRITE

    /** POST /schools - daftarkan sekolah (FR-01). */
    public function store(StoreSchoolRequest $request, SchoolImporter $importer, CreateSchoolAction $create): JsonResponse
    {
        $data = $request->validated();

        $regionId = $importer->regionIdFor($data);
        if ($regionId === null) {
            return $this->fail(422, 'Koordinat tidak berada di dalam batas wilayah mana pun.', [
                'region_id' => ['Kirim region_id secara manual atau impor batas wilayah terlebih dahulu.'],
            ]);
        }

        try {
            $id = $create->insert($data, $regionId, self::API_SOURCE);
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23505') {
                return $this->fail(409, 'NPSN sudah terdaftar (mungkin berstatus nonaktif).');
            }
            throw $e;
        }

        return $this->ok($this->schools->findActive($id), null, 201);
    }

    /** PUT /schools/{id} - ubah kontak dan akreditasi saja. */
    public function update(UpdateSchoolRequest $request, int $id, UpdateSchoolContactAction $update): JsonResponse
    {
        $data = $request->validated();
        if ($data === []) {
            return $this->fail(422, 'Tidak ada field yang diubah (accreditation, contact_phone, contact_email).');
        }

        return $update->execute($id, $data)
            ? $this->ok($this->schools->findActive($id))
            : $this->fail(404, 'Sekolah tidak ditemukan.');
    }

    /** DELETE /schools/{id} - soft delete: is_active = false (FR-01). */
    public function destroy(int $id, DeactivateSchoolAction $deactivate): JsonResponse
    {
        return $deactivate->execute($id)
            ? response()->json(null, 204)
            : $this->fail(404, 'Sekolah tidak ditemukan.');
    }

    /**
     * POST /schools/import - impor massal, upsert berdasarkan NPSN (FR-01, TR-08).
     * Logika penulisan ada di SchoolImporter agar dipakai bersama sinkronisasi (schools:sync).
     */
    public function import(ImportSchoolsRequest $request, SchoolImporter $importer): JsonResponse
    {
        return $this->ok($importer->import($request->input('schools')));
    }
}
