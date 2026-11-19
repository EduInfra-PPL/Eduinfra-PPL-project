<?php

namespace App\Actions\FacilityCategories;

use App\Models\FacilityCategory;

/**
 * Mengubah bobot urgensi, satuan bawaan, atau status aktif kategori (FR-03, U).
 * Nama dan sub-kategori sengaja tidak bisa diubah karena dirujuk laporan kerusakan.
 */
class UpdateFacilityCategoryAction
{
    private const EDITABLE = ['urgency_weight', 'default_unit', 'is_enabled'];

    /** @return FacilityCategory|null null bila kategori tidak ditemukan */
    public function execute(int $id, array $data): ?FacilityCategory
    {
        $category = FacilityCategory::find($id);
        if (! $category) {
            return null;
        }

        $changes = array_intersect_key($data, array_flip(self::EDITABLE));
        if (array_key_exists('default_unit', $changes) && ! filled($changes['default_unit'])) {
            $changes['default_unit'] = null;
        }

        if ($changes !== []) {
            $category->update($changes);
        }

        return $category->fresh();
    }
}
