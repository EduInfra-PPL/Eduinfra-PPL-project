<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\FacilityCategories\CreateFacilityCategoryAction;
use App\Actions\FacilityCategories\DeactivateFacilityCategoryAction;
use App\Actions\FacilityCategories\DuplicateFacilityCategoryException;
use App\Actions\FacilityCategories\ListFacilityCategoriesAction;
use App\Actions\FacilityCategories\UpdateFacilityCategoryAction;
use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\FacilityCategories\StoreFacilityCategoryRequest;
use App\Http\Requests\FacilityCategories\UpdateFacilityCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * PBI-03 / FR-03: kategori/sub-kategori fasilitas dan bobot urgensi 1-5.
 * C = tambah kategori, R = taksonomi, U = ubah bobot urgensi, D = nonaktifkan.
 * Tipis: validasi di FormRequest, logika di Action (dipakai bersama halaman admin).
 */
class FacilityCategoryController extends Controller
{
    use ApiResponses;

    /** GET /facility-categories - default hanya yang aktif; ?include_disabled=1 untuk semua. */
    public function index(Request $request, ListFacilityCategoriesAction $list): JsonResponse
    {
        return $this->ok($list->all($request->boolean('include_disabled')));
    }

    /** POST /facility-categories */
    public function store(StoreFacilityCategoryRequest $request, CreateFacilityCategoryAction $create): JsonResponse
    {
        try {
            $category = $create->execute($request->validated());
        } catch (DuplicateFacilityCategoryException $e) {
            return $this->fail(409, $e->getMessage());
        }

        return $this->ok($category, null, 201);
    }

    /** PATCH /facility-categories/{id} - ubah bobot urgensi (1-5), satuan, atau aktifkan kembali. */
    public function update(UpdateFacilityCategoryRequest $request, int $id, UpdateFacilityCategoryAction $update): JsonResponse
    {
        $data = $request->validated();

        $category = $update->execute($id, $data);
        if (! $category) {
            return $this->fail(404, 'Kategori tidak ditemukan.');
        }
        if ($data === []) {
            return $this->fail(422, 'Tidak ada field yang diubah (urgency_weight, default_unit, is_enabled).');
        }

        return $this->ok($category);
    }

    /** DELETE /facility-categories/{id} - nonaktifkan (kategori dirujuk laporan, jadi tidak dihapus). */
    public function destroy(int $id, DeactivateFacilityCategoryAction $deactivate): JsonResponse
    {
        if (! $deactivate->execute($id)) {
            return $this->fail(404, 'Kategori tidak ditemukan.');
        }

        return response()->json(null, 204);
    }
}
