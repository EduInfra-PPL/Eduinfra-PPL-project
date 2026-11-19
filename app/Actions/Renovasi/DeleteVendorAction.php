<?php

namespace App\Actions\Renovasi;

use App\Models\RepairProject;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class DeleteVendorAction
{
    public function execute(Vendor $vendor): string
    {
        return DB::transaction(function () use ($vendor) {
            $name = $vendor->company_name;

            RepairProject::where('vendor_id', $vendor->id)
                ->update(['vendor_id' => null]);

            $vendor->delete();

            return $name;
        });
    }
}
