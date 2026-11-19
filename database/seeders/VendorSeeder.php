<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\RepairProject;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $findRegionId = function (string $code, string $fallbackName = ''): ?int {
            $region = Region::where('code', $code)->first();
            if ($region) {
                return $region->id;
            }

            if ($fallbackName !== '') {
                $byName = Region::where('name', 'like', "%{$fallbackName}%")->first();
                if ($byName) {
                    return $byName->id;
                }
            }

            return Region::where('level', 'kab')->orderByDesc('is_3t_tier')->first()?->id
                ?? Region::first()?->id;
        };

        $vendorsData = [
            [
                'license_num' => 'LPJK-3829-NTT',
                'company_name' => 'CV Alor Mandiri Konstruksi (LPJK)',
                'entity_type' => 'CV',
                'nib' => '7120198823100',
                'contact_phone' => '0812-3940-8812',
                'service_region_id' => $findRegionId('53.05', 'Alor'),
                'grade' => 'K3',
                'qualification_label' => 'Kecil - Bangunan Gedung Pendidikan',
                'max_contract_limit' => 2_500_000_000,
                'sbu_status' => 'verified',
                'sbu_valid_until' => '2027-12-31',
                'specialty' => 'Spesialis Atap Gempa & Baja Ringan SNI',
                'logistics_note' => 'Armada Kapal Pelataran & Angkutan Material Kepulauan',
                'lead_engineer' => 'Ir. Hendra Pratama, ST',
                'engineer_role' => 'PJBU • SKA Madya Bangunan Gedung',
                'rating' => 4.90,
                'review_count' => 8,
                'on_time_pct' => 98.20,
                'is_active' => true,
            ],
            [
                'license_num' => 'LPJK-4102-NTT',
                'company_name' => 'KSO Alor Konstruksi Nusantara',
                'entity_type' => 'KSO',
                'nib' => '8129003441923',
                'contact_phone' => '0813-3811-4509',
                'service_region_id' => $findRegionId('53.05', 'Alor'),
                'grade' => 'M1',
                'qualification_label' => 'Menengah - Konstruksi Fisik & Drainase Pesisir',
                'max_contract_limit' => 10_000_000_000,
                'sbu_status' => 'verified',
                'sbu_valid_until' => '2027-08-30',
                'specialty' => 'Struktur RKB Tahan Gempa & Bronjong Pesisir',
                'logistics_note' => 'Armada Truk 4WD, Concrete Mixer & Ponton Pesisir',
                'lead_engineer' => 'Markus D. Lado, ST',
                'engineer_role' => 'PJBU • Ahli Struktur Bangunan 3T',
                'rating' => 4.80,
                'review_count' => 14,
                'on_time_pct' => 96.50,
                'is_active' => true,
            ],
            [
                'license_num' => 'LPJK-0118-BSR',
                'company_name' => 'PT Sucofindo Taskforce Sarpras',
                'entity_type' => 'BUMN / PT',
                'nib' => '0100293817729',
                'contact_phone' => '0811-4800-9910',
                'service_region_id' => $findRegionId('93.04', 'Asmat'),
                'grade' => 'B1',
                'qualification_label' => 'Besar & Pengawas Teknis Konstruksi Nasional',
                'max_contract_limit' => 50_000_000_000,
                'sbu_status' => 'verified',
                'sbu_valid_until' => '2028-12-31',
                'specialty' => 'Konstruksi Rawa Pasang Surut & Filtrasi Air Bersih',
                'logistics_note' => 'Akses Kapal LCT, Longboat Sungai & Helipad Perintis',
                'lead_engineer' => 'Dr. Ir. Wahyudi, MT',
                'engineer_role' => 'Lead Auditor • SKA Utama Manajemen Konstruksi',
                'rating' => 5.00,
                'review_count' => 28,
                'on_time_pct' => 99.40,
                'is_active' => true,
            ],
            [
                'license_num' => 'LPJK-5541-PAP',
                'company_name' => 'CV Prasarana Mandiri Papua',
                'entity_type' => 'CV',
                'nib' => '9120019283741',
                'contact_phone' => '0812-4772-6120',
                'service_region_id' => $findRegionId('95.02', 'Pegunungan Bintang'),
                'grade' => 'K3',
                'qualification_label' => 'Kecil - Prasarana Pendidikan & Kelistrikan Surya',
                'max_contract_limit' => 2_500_000_000,
                'sbu_status' => 'verified',
                'sbu_valid_until' => '2027-06-30',
                'specialty' => 'Laboratorium Sekolah & PLTS Komunal Off-Grid',
                'logistics_note' => 'Kargo Udara Perintis & Armada Angkut Pegunungan',
                'lead_engineer' => 'Sulaiman, ST',
                'engineer_role' => 'PJBU • SKA Madya Teknik Sipil & Elektrikal',
                'rating' => 4.85,
                'review_count' => 11,
                'on_time_pct' => 97.80,
                'is_active' => true,
            ],
            [
                'license_num' => 'LPJK-2290-NTT',
                'company_name' => 'CV Timor Raya Bangun Mandiri',
                'entity_type' => 'CV',
                'nib' => '6128892019284',
                'contact_phone' => '0821-4610-3391',
                'service_region_id' => $findRegionId('53.02', 'Timor Tengah Selatan'),
                'grade' => 'K1',
                'qualification_label' => 'Kecil - Pekerjaan Kayu, Pasangan Bata & Atap',
                'max_contract_limit' => 1_000_000_000,
                'sbu_status' => 'review',
                'sbu_valid_until' => '2026-12-15',
                'specialty' => 'Perkuatan Dinding Bata & Penutup Atap Galvalume',
                'logistics_note' => 'Akses Jalur Lintas Darat Perbatasan Timor',
                'lead_engineer' => 'Paulus Mau, AMd',
                'engineer_role' => 'PJBU • Pelaksana Madya Bangunan Gedung',
                'rating' => 4.60,
                'review_count' => 5,
                'on_time_pct' => 91.00,
                'is_active' => true,
            ],
            [
                'license_num' => 'LPJK-7741-NDG',
                'company_name' => 'CV Karya Pegunungan Konstruksi',
                'entity_type' => 'CV',
                'nib' => '9120401837492',
                'contact_phone' => '0811-4920-7714',
                'service_region_id' => $findRegionId('95.08', 'Nduga'),
                'grade' => 'K3',
                'qualification_label' => 'Kecil - Sanitasi Sekolah & Bangunan Pendidikan',
                'max_contract_limit' => 2_500_000_000,
                'sbu_status' => 'verified',
                'sbu_valid_until' => '2027-10-31',
                'specialty' => 'Spesialis Sanitasi SNI & Septic Tank Biofilter',
                'logistics_note' => 'Logistik Terpadu Koridor Pegunungan Tengah',
                'lead_engineer' => 'Hendri Kusuma, ST',
                'engineer_role' => 'PJBU • Ahli K3 Konstruksi Madya',
                'rating' => 4.75,
                'review_count' => 7,
                'on_time_pct' => 94.50,
                'is_active' => true,
            ],
            [
                'license_num' => 'LPJK-9102-JYW',
                'company_name' => 'PT Baliem Konstruksi Mandiri',
                'entity_type' => 'PT',
                'nib' => '8129007712340',
                'contact_phone' => '0812-4881-2090',
                'service_region_id' => $findRegionId('95.01', 'Jayawijaya'),
                'grade' => 'M1',
                'qualification_label' => 'Menengah - Konstruksi Ruang Praktik & Laboratorium',
                'max_contract_limit' => 10_000_000_000,
                'sbu_status' => 'verified',
                'sbu_valid_until' => '2028-05-31',
                'specialty' => 'Struktur Baja & Rehabilitasi Ruang Laboratorium IPA',
                'logistics_note' => 'Gudang Material Wamena & Armada Dump Truck',
                'lead_engineer' => 'Ir. Yohanes Pahabol, ST',
                'engineer_role' => 'PJBU • SKA Madya Struktur Gedung',
                'rating' => 4.88,
                'review_count' => 12,
                'on_time_pct' => 97.10,
                'is_active' => true,
            ],
            [
                'license_num' => 'LPJK-6610-KPG',
                'company_name' => 'CV Flobamora Teknik Sarpras',
                'entity_type' => 'CV',
                'nib' => '5120098123019',
                'contact_phone' => '0813-3901-5521',
                'service_region_id' => $findRegionId('53.71', 'Kupang'),
                'grade' => 'K2',
                'qualification_label' => 'Kecil - Instalasi Mekanikal, Elektrikal & Sanitasi',
                'max_contract_limit' => 1_500_000_000,
                'sbu_status' => 'verified',
                'sbu_valid_until' => '2027-09-30',
                'specialty' => 'Instalasi Jaringan Listrik Sekolah & Perpipaan Air',
                'logistics_note' => 'Armada Distribusi Material Timor & Kepulauan',
                'lead_engineer' => 'Ir. Стеfanus Lake, ST',
                'engineer_role' => 'PJBU • SKA Muda Elektrikal Bangunan',
                'rating' => 4.70,
                'review_count' => 9,
                'on_time_pct' => 95.00,
                'is_active' => true,
            ],
        ];

        foreach ($vendorsData as $data) {
            // Ensure clean ASCII in lead_engineer
            $data['lead_engineer'] = str_replace('Стеfanus', 'Stefanus', $data['lead_engineer']);

            $vendor = Vendor::updateOrCreate(
                ['license_num' => $data['license_num']],
                $data
            );

            RepairProject::where('contractor_name', $vendor->company_name)
                ->update(['vendor_id' => $vendor->id]);
        }
    }
}
