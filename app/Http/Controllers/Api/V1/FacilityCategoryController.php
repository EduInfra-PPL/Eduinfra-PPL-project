<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Models\FacilityCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * PBI-03 / FR-03: kategori/sub-kategori fasilitas dan bobot urgensi 1-5.
 * C = tambah kategori, R = taksonomi, U = ubah bobot urgensi, D = nonaktifkan.
 * (Tidak tercantum di Tabel 15, tetapi dibutuhkan FR-03.)
 */
class FacilityCategoryController extends Controller
{
    use ApiResponses;

    /** GET /facility-categories - default hanya yang aktif; ?include_disabled=1 untuk semua. */
    public function index(Request $request): JsonResponse
    {
        $v = $request->validate(['include_disabled' => ['nullable', 'in:true,false,1,0']]);
        $all = filter_var($v['include_disabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $rows = FacilityCategory::query()
            ->when(! $all, fn ($q) => $q->where('is_enabled', true))
            ->orderBy('name')->orderBy('sub_category')
            ->get(['id', 'name', 'sub_category', 'urgency_weight', 'default_unit', 'is_enabled']);

        return $this->ok($rows);
    }

    /** POST /facility-categories */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'urgency_weight' => ['required', 'integer', 'between:1,5'],
            'default_unit' => ['nullable', 'string', 'max:30'],
        ]);
        $data['sub_category'] = $data['sub_category'] ?? '';

        try {
            $category = DB::transaction(fn () => FacilityCategory::create($data));
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23505') {
                return $this->fail(409, 'Kategori dengan nama dan sub-kategori yang sama sudah ada.');
            }
            throw $e;
        }

        return $this->ok($category->fresh(), null, 201);
    }

    /** PATCH /facility-categories/{id} - ubah bobot urgensi (1-5), satuan, atau aktifkan kembali. */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'urgency_weight' => ['sometimes', 'required', 'integer', 'between:1,5'],
            'default_unit' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_enabled' => ['sometimes', 'required', 'boolean'],
            'name' => ['prohibited'],
            'sub_category' => ['prohibited'],
        ]);

        $category = FacilityCategory::find($id);
        if (! $category) {
            return $this->fail(404, 'Kategori tidak ditemukan.');
        }
        if ($data === []) {
            return $this->fail(422, 'Tidak ada field yang diubah (urgency_weight, default_unit, is_enabled).');
        }

        $category->update($data);

        return $this->ok($category->fresh());
    }

    /** DELETE /facility-categories/{id} - nonaktifkan (kategori dirujuk laporan, jadi tidak dihapus). */
    public function destroy(int $id): JsonResponse
    {
        $category = FacilityCategory::find($id);
        if (! $category) {
            return $this->fail(404, 'Kategori tidak ditemukan.');
        }

        $category->update(['is_enabled' => false]);

        return response()->json(null, 204);
    }
}
