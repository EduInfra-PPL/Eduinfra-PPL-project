<?php

namespace App\Actions\Renovasi;

use App\Models\RepairProject;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class UpdateVendorAction
{
    public function execute(Vendor $vendor, array $data): Vendor
    {
        return DB::transaction(function () use ($vendor, $data) {
            $grade = $data['grade'] ?? $vendor->grade;

            $vendor->update([
                'company_name' => $data['company_name'],
                'entity_type' => $data['entity_type'] ?? $vendor->entity_type,
                'license_num' => $data['license_num'],
                'nib' => $data['nib'] ?? $vendor->nib,
                'contact_phone' => $data['contact_phone'],
                'service_region_id' => $data['service_region_id'],
                'grade' => $grade,
                'qualification_label' => ($data['qualification_label'] ?? null) ?: $vendor->qualification_label,
                'max_contract_limit' => $data['max_contract_limit'] ?? $vendor->max_contract_limit,
                'sbu_status' => $data['sbu_status'] ?? $vendor->sbu_status,
                'sbu_valid_until' => ($data['sbu_valid_until'] ?? null) ?: $vendor->sbu_valid_until,
                'specialty' => ($data['specialty'] ?? null) ?: $vendor->specialty,
                'logistics_note' => ($data['logistics_note'] ?? null) ?: $vendor->logistics_note,
                'lead_engineer' => ($data['lead_engineer'] ?? null) ?: $vendor->lead_engineer,
                'engineer_role' => ($data['engineer_role'] ?? null) ?: $vendor->engineer_role,
                'rating' => $data['rating'] ?? $vendor->rating,
                'is_active' => (bool) ($data['is_active'] ?? $vendor->is_active),
            ]);

            // Sinkronisasi nama kontraktor pada proyek yang ditangani vendor ini
            RepairProject::where('vendor_id', $vendor->id)
                ->update(['contractor_name' => $vendor->company_name]);

            if (! empty($data['assign_project_id']) && $vendor->is_active) {
                $project = RepairProject::find($data['assign_project_id']);
                $project?->update([
                    'vendor_id' => $vendor->id,
                    'contractor_name' => $vendor->company_name,
                ]);
            }

            return $vendor->fresh(['serviceRegion', 'repairProjects.school']);
        });
    }
}
