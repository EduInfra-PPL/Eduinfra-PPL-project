<?php

namespace App\Actions\Renovasi;

use App\Models\RepairProject;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class CreateVendorAction
{
    public function execute(array $data): Vendor
    {
        return DB::transaction(function () use ($data) {
            $grade = $data['grade'] ?? 'K3';
            $defaultQualification = match ($grade) {
                'K1' => 'Kecil - Pekerjaan Kayu & Bata',
                'K2' => 'Kecil - Renovasi Rangka & Atap',
                'K3' => 'Kecil - Bangunan Gedung Pendidikan',
                'M1', 'M2' => 'Menengah - Konstruksi Fisik & Sanitasi',
                'B1', 'B2' => 'Besar & Pengawas Teknis Konstruksi',
                default => 'Kecil - Bangunan Gedung Pendidikan',
            };

            $vendor = Vendor::create([
                'company_name' => $data['company_name'],
                'entity_type' => $data['entity_type'] ?? 'CV',
                'license_num' => $data['license_num'],
                'nib' => $data['nib'] ?? null,
                'contact_phone' => $data['contact_phone'],
                'service_region_id' => $data['service_region_id'],
                'grade' => $grade,
                'qualification_label' => ($data['qualification_label'] ?? null) ?: $defaultQualification,
                'max_contract_limit' => $data['max_contract_limit'],
                'sbu_status' => $data['sbu_status'] ?? 'verified',
                'sbu_valid_until' => ($data['sbu_valid_until'] ?? null) ?: now()->addYears(2)->toDateString(),
                'specialty' => ($data['specialty'] ?? null) ?: 'Ahli Sarpras 3T',
                'logistics_note' => ($data['logistics_note'] ?? null) ?: 'Armada Angkutan Material & Peralatan Mandiri',
                'lead_engineer' => ($data['lead_engineer'] ?? null) ?: 'Penanggung Jawab Teknis LPJK',
                'engineer_role' => ($data['engineer_role'] ?? null) ?: 'PJBU • SKA Konstruksi Bangunan',
                'rating' => $data['rating'] ?? 4.80,
                'review_count' => 1,
                'on_time_pct' => 96.00,
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            if (! empty($data['assign_project_id']) && $vendor->is_active) {
                $project = RepairProject::find($data['assign_project_id']);
                $project?->update([
                    'vendor_id' => $vendor->id,
                    'contractor_name' => $vendor->company_name,
                ]);
            }

            return $vendor;
        });
    }
}
