<?php

namespace App\Actions\Regions;

use App\Models\Region;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/** Hapus batas wilayah (FR-02). Ditolak bila wilayah masih dirujuk sekolah. */
class DeleteRegionAction
{
    /**
     * Kode SQLSTATE pelanggaran foreign key. schools.region_id memakai ON DELETE RESTRICT
     * yang menghasilkan 23001; 23503 untuk FK dengan aturan NO ACTION.
     */
    private const FK_VIOLATIONS = ['23001', '23503'];

    /**
     * @return bool false bila wilayah tidak ditemukan
     *
     * @throws RegionInUseException
     */
    public function execute(int $regionId): bool
    {
        try {
            return DB::transaction(fn () => Region::query()->whereKey($regionId)->delete()) > 0;
        } catch (QueryException $e) {
            if (in_array((string) $e->getCode(), self::FK_VIOLATIONS, true)) {
                throw new RegionInUseException;
            }
            throw $e;
        }
    }
}
