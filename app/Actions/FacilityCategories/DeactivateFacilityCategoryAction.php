<?php

namespace App\Actions\FacilityCategories;

use App\Models\FacilityCategory;

/**
 * Menonaktifkan kategori (FR-03, D). Baris tidak dihapus karena dirujuk laporan
 * kerusakan; kategori nonaktif hanya tidak muncul lagi di formulir laporan.
 */
class DeactivateFacilityCategoryAction
{
    /** @return bool false bila kategori tidak ditemukan */
    public function execute(int $id): bool
    {
        $category = FacilityCategory::find($id);
        if (! $category) {
            return false;
        }

        if ($category->is_enabled) {
            $category->update(['is_enabled' => false]);
        }

        return true;
    }
}
