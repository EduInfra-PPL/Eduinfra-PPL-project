<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Regions\DeleteRegionAction;
use App\Actions\Regions\ImportRegionsAction;
use App\Actions\Regions\RegionInUseException;
use App\Actions\Regions\UpdateRegionCategoriesAction;
use App\Actions\Regions\UpdateRegionTierAction;
use App\Enums\ThreeTCategory;
use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Regions\ImportRegionsRequest;
use App\Http\Requests\Api\V1\Regions\LookupRegionRequest;
use App\Http\Requests\Api\V1\Regions\RegionsGeoJsonRequest;
use App\Http\Requests\Api\V1\Regions\SearchRegionsRequest;
use App\Http\Requests\Api\V1\Regions\UpdateRegionCategoriesRequest;
use App\Http\Requests\Api\V1\Regions\UpdateRegionTierRequest;
use App\Services\Regions\RegionQueryService;
use Illuminate\Http\JsonResponse;

/**
 * PBI-02 / FR-02: batas wilayah administrasi dan penanda 3T.
 * Operasi sesuai Tabel 16: C = impor GeoJSON, R = poligon, U = status 3T, D = hapus batas.
 */
class RegionController extends Controller
{
    use ApiResponses;

    public function __construct(private RegionQueryService $regions) {}

    // ------------------------------------------------------------------ READ

    /** GET /regions - daftar wilayah tanpa geometri. */
    public function index(SearchRegionsRequest $request): JsonResponse
    {
        $page = $this->regions->paginate($request->filters(), $request->perPage());

        return $this->ok($page->items(), $this->pageMeta($page));
    }

    /** GET /regions/{id} - detail + poligon (GeoJSON geometry). */
    public function show(int $id): JsonResponse
    {
        $region = $this->regions->findWithGeometry($id);

        return $region ? $this->ok($region) : $this->fail(404, 'Wilayah tidak ditemukan.');
    }

    /** GET /regions/geojson - FeatureCollection untuk peta/choropleth (respons GeoJSON murni). */
    public function geojson(RegionsGeoJsonRequest $request): JsonResponse
    {
        return response()->json($this->regions->geoJson($request->filters(), $request->simplify()));
    }

    /** GET /regions/lookup?lat=&lng= - wilayah yang memuat titik (paling spesifik lebih dulu). */
    public function lookup(LookupRegionRequest $request): JsonResponse
    {
        return $this->ok($this->regions->containingPoint($request->validated('lat'), $request->validated('lng')));
    }

    // ----------------------------------------------------------------- WRITE

    /** POST /regions/import - impor GeoJSON FeatureCollection, upsert berdasarkan code (FR-02). */
    public function import(ImportRegionsRequest $request, ImportRegionsAction $import): JsonResponse
    {
        return $this->ok($import->execute($request->input('features')));
    }

    /** PATCH /regions/{id}/tier - perbarui tingkat prioritas 3T (FR-02). */
    public function updateTier(UpdateRegionTierRequest $request, int $id, UpdateRegionTierAction $update): JsonResponse
    {
        if (! $update->execute($id, (int) $request->validated('is_3t_tier'))) {
            return $this->fail(404, 'Wilayah tidak ditemukan.');
        }

        return $this->ok($this->regions->findSummary($id));
    }

    /**
     * PATCH /regions/{id}/categories - atur kategori 3T (tertinggal/terdepan/terluar).
     * Body: { is_tertinggal?, is_terdepan?, is_terluar? } - minimal satu harus dikirim.
     */
    public function updateCategories(UpdateRegionCategoriesRequest $request, int $id, UpdateRegionCategoriesAction $action): JsonResponse
    {
        $region = $action->execute($id, $request->validated());
        if ($region === null) {
            return $this->fail(404, 'Wilayah tidak ditemukan.');
        }

        return $this->ok(ThreeTCategory::decorate((object) $region->toArray()));
    }

    /** DELETE /regions/{id} - hapus batas wilayah; ditolak 409 bila masih dipakai sekolah. */
    public function destroy(int $id, DeleteRegionAction $delete): JsonResponse
    {
        try {
            $deleted = $delete->execute($id);
        } catch (RegionInUseException $e) {
            return $this->fail(409, $e->getMessage());
        }

        return $deleted ? response()->json(null, 204) : $this->fail(404, 'Wilayah tidak ditemukan.');
    }
}
