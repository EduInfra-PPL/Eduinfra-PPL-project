<?php

namespace App\Actions\Schools;

use App\Models\School;

/** Nonaktifkan sekolah (soft delete, FR-01). Data tetap disimpan dan bisa diaktifkan lagi. */
class DeactivateSchoolAction
{
    public function execute(int $id): bool
    {
        return School::query()->active()->where('id', $id)->update(['is_active' => false]) > 0;
    }
}
