<?php

namespace App\Actions\FacilityCategories;

use App\Models\FacilityCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/** Menambah kategori/sub-kategori fasilitas baru (FR-03, C). */
class CreateFacilityCategoryAction
{
    /**
     * @param  array{name: string, sub_category?: string|null, urgency_weight: int, default_unit?: string|null}  $data
     *
     * @throws DuplicateFacilityCategoryException
     */
    public function execute(array $data): FacilityCategory
    {
        $attributes = [
            'name' => $data['name'],
            // kolom NOT NULL DEFAULT '' supaya constraint unik (name, sub_category) tetap berlaku
            'sub_category' => $data['sub_category'] ?? '',
            'urgency_weight' => (int) $data['urgency_weight'],
            'default_unit' => filled($data['default_unit'] ?? null) ? $data['default_unit'] : null,
        ];

        try {
            // savepoint: bila insert gagal, transaksi luar (mis. saat pengujian) tetap bisa dipakai
            $category = DB::transaction(fn () => FacilityCategory::create($attributes));
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23505') {
                throw new DuplicateFacilityCategoryException;
            }
            throw $e;
        }

        return $category->fresh();
    }
}
