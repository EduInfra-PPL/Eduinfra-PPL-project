<?php

namespace Database\Seeders;

use App\Models\Disbursement;
use App\Models\RepairProject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DisbursementSeeder extends Seeder
{
    /**
     * Mengisi data awal mutasi pencairan anggaran ke vendor (`disbursements` — PBI-12)
     * berdasarkan proyek renovasi yang tersedia di Domain 3.
     */
    public function run(): void
    {
        $projects = RepairProject::query()->orderBy('id')->get();
        if ($projects->isEmpty()) {
            return;
        }

        $authorizerId = User::query()
            ->where('role_enum', 'admin_dinas')
            ->value('id') ?? User::query()->value('id');

        $p1 = $projects[0];
        $p2 = $projects[1] ?? $p1;
        $p3 = $projects[2] ?? $p1;

        $rows = [
            [
                'transaction_code' => 'TRX-DISB-2026-104',
                'project_id' => $p1->id,
                'stage_num' => 2,
                'amount' => 168_000_000,
                'receipt_file' => 'Kuitansi_Termin2_Signed.pdf',
                'tax_invoice_file' => 'eFaktur_Termin2_104.pdf',
                'disbursed_at' => now()->subDays(2),
                'authorized_by' => $authorizerId,
                'vendor_name' => $p1->contractor_name ?: 'CV Sentosa Sarpras',
                'vendor_bank_account' => 'Bank Mandiri •• 8821',
                'status' => 'disbursed',
            ],
            [
                'transaction_code' => 'TRX-DISB-2026-103',
                'project_id' => $p2->id,
                'stage_num' => 1,
                'amount' => 90_000_000,
                'receipt_file' => 'Kuitansi_Termin1_Signed.pdf',
                'tax_invoice_file' => null,
                'disbursed_at' => now()->subDays(6),
                'authorized_by' => $authorizerId,
                'vendor_name' => $p2->contractor_name ?: 'PT Bahari Konstruksi',
                'vendor_bank_account' => 'BNI Giro •• 1092',
                'status' => 'disbursed',
            ],
            [
                'transaction_code' => 'TRX-DISB-2026-101',
                'project_id' => $p3->id,
                'stage_num' => 3,
                'amount' => 126_000_000,
                'receipt_file' => 'Kuitansi_Termin3_Final.pdf',
                'tax_invoice_file' => 'eFaktur_Final_101.pdf',
                'disbursed_at' => now()->subDays(12),
                'authorized_by' => $authorizerId,
                'vendor_name' => $p3->contractor_name ?: 'CV Cendrawasih Karya',
                'vendor_bank_account' => 'Bank Papua •• 3499',
                'status' => 'disbursed',
            ],
            [
                'transaction_code' => 'TRX-DISB-2026-098',
                'project_id' => $p1->id,
                'stage_num' => 1,
                'amount' => 126_000_000,
                'receipt_file' => 'Kuitansi_Termin1_Sambas.pdf',
                'tax_invoice_file' => null,
                'disbursed_at' => now()->subDays(18),
                'authorized_by' => $authorizerId,
                'vendor_name' => $p1->contractor_name ?: 'CV Sentosa Sarpras',
                'vendor_bank_account' => 'Bank Mandiri •• 8821',
                'status' => 'disbursed',
            ],
        ];

        foreach ($rows as $row) {
            Disbursement::updateOrCreate(
                ['transaction_code' => $row['transaction_code']],
                $row
            );
        }
    }
}
