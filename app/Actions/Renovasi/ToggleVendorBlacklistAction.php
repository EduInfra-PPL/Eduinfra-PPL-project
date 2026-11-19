<?php

namespace App\Actions\Renovasi;

use App\Models\Vendor;

class ToggleVendorBlacklistAction
{
    public function execute(Vendor $vendor): Vendor
    {
        $newActiveState = ! $vendor->is_active;

        $vendor->update([
            'is_active' => $newActiveState,
            'sbu_status' => $newActiveState ? 'verified' : 'review',
        ]);

        return $vendor->fresh();
    }
}
