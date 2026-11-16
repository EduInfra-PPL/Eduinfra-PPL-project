<?php

namespace App\Actions\Regions;

use App\Models\Region;
use Illuminate\Support\Facades\DB;

/** Perbarui kategori 3T (tertinggal/terdepan/terluar) satu wilayah. */
class UpdateRegionCategoriesAction
{
    /**
     * @param  array<string, bool>  $categories  hanya kunci kolom kategori yang dikirim
     * @return Region|null  null bila wilayah tidak ditemukan
     */
    public function execute(int $regionId, array $categories): ?Region
    {
        return DB::transaction(function () use ($regionId, $categories): ?Region {
            $affected = Region::query()->whereKey($regionId)->update($categories);
            if ($affected === 0) {
                return null;
            }

            return Region::query()->select(Region::COLUMNS)->find($regionId);
        });
    }
}
