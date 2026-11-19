<?php

namespace App\Actions\Regions;

use App\Models\Region;

/** Perbarui tingkat prioritas 3T (is_3t_tier) satu wilayah (FR-02). */
class UpdateRegionTierAction
{
    /** @return bool false bila wilayah tidak ditemukan */
    public function execute(int $regionId, int $tier): bool
    {
        return Region::query()->whereKey($regionId)->update(['is_3t_tier' => $tier]) > 0;
    }
}
