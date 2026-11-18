<?php

namespace Database\Seeders;

use App\Models\AidCampaign;
use App\Models\AidContribution;
use App\Models\User;
use Illuminate\Database\Seeder;

class AidContributionSeeder extends Seeder
{
    /**
     * Mengisi data awal riwayat kontribusi donatur CSR (`aid_contributions`)
     * agar dashboard Wall of Donors dan Audit Log langsung terisi data riil.
     */
    public function run(): void
    {
        $campaigns = AidCampaign::query()->orderBy('id')->get();

        if ($campaigns->isEmpty()) {
            return;
        }

        $firstCampaignId = $campaigns[0]->id;
        $secondCampaignId = $campaigns[1]->id ?? $firstCampaignId;
        $thirdCampaignId = $campaigns[2]->id ?? $firstCampaignId;

        $defaultDonorId = User::query()->value('id');

        $seedRows = [
            [
                'transaction_code' => 'TRX-CSR-041',
                'campaign_id' => $firstCampaignId,
                'donor_id' => $defaultDonorId,
                'donor_name' => 'PT Telkom Indonesia Tbk',
                'donor_unit' => 'CSR Divre VII',
                'amount_or_goods' => 150_000_000,
                'contribution_type' => 'cash',
                'payment_proof' => 'BAP_Termin1.pdf',
                'term_stage' => 'Termin 1 (Pondasi)',
                'verification_note' => 'Drone Terverifikasi',
                'notes' => 'Komitmen CSR Pilar Pendidikan Digital & Konstruksi 3T.',
                'status' => 'verified',
            ],
            [
                'transaction_code' => 'TRX-CSR-039',
                'campaign_id' => $secondCampaignId,
                'donor_id' => $defaultDonorId,
                'donor_name' => 'PT Astra International Tbk',
                'donor_unit' => 'Yayasan Pendidikan Astra',
                'amount_or_goods' => 180_000_000,
                'contribution_type' => 'cash',
                'payment_proof' => 'BAP_Termin2.pdf',
                'term_stage' => 'Termin 2 (Struktur)',
                'verification_note' => 'Pasang-Surut Selesai',
                'notes' => 'Bantuan perbaikan struktur atap dan sanitasi bersih.',
                'status' => 'verified',
            ],
            [
                'transaction_code' => 'TRX-CSR-035',
                'campaign_id' => $thirdCampaignId,
                'donor_id' => $defaultDonorId,
                'donor_name' => 'PT Bank Rakyat Indonesia Tbk',
                'donor_unit' => 'BRI Peduli 3T',
                'amount_or_goods' => 255_000_000,
                'contribution_type' => 'cash',
                'payment_proof' => 'BAP_Final.pdf',
                'term_stage' => 'Termin 3 Final',
                'verification_note' => '100% Serah Terima',
                'notes' => 'Program BRI Peduli Revitalisasi Sekolah Perbatasan.',
                'status' => 'verified',
            ],
            [
                'transaction_code' => 'TRX-CSR-032',
                'campaign_id' => $firstCampaignId,
                'donor_id' => $defaultDonorId,
                'donor_name' => 'PT Indofood CBP Tbk',
                'donor_unit' => 'Indofood Peduli',
                'amount_or_goods' => 95_000_000,
                'contribution_type' => 'goods',
                'payment_proof' => 'BAP_Material01.pdf',
                'term_stage' => 'Termin 1 (Pondasi)',
                'verification_note' => 'Drone Terverifikasi',
                'notes' => 'Bantuan material konstruksi & perlengkapan belajar siswa.',
                'status' => 'verified',
            ],
            [
                'transaction_code' => 'TRX-CSR-029',
                'campaign_id' => $secondCampaignId,
                'donor_id' => $defaultDonorId,
                'donor_name' => 'Yayasan Bakti BCA',
                'donor_unit' => 'Bakti Pendidikan',
                'amount_or_goods' => 75_000_000,
                'contribution_type' => 'cash',
                'payment_proof' => 'BAP_Termin1_BCA.pdf',
                'term_stage' => 'Termin 1 (Pondasi)',
                'verification_note' => '100% Serah Terima',
                'notes' => 'Dukungan rehabilitasi fasilitas ruang baca & laboratorium.',
                'status' => 'verified',
            ],
            [
                'transaction_code' => 'TRX-CSR-025',
                'campaign_id' => $thirdCampaignId,
                'donor_id' => $defaultDonorId,
                'donor_name' => 'MIND ID',
                'donor_unit' => 'TJSL Holding Pertambangan',
                'amount_or_goods' => 60_000_000,
                'contribution_type' => 'cash',
                'payment_proof' => 'BAP_Termin2_MIND.pdf',
                'term_stage' => 'Termin 2 (Struktur)',
                'verification_note' => 'Drone Terverifikasi',
                'notes' => 'Alokasi dana CSR infrastruktur pendidikan wilayah timur.',
                'status' => 'verified',
            ],
        ];

        foreach ($seedRows as $row) {
            AidContribution::updateOrCreate(
                ['transaction_code' => $row['transaction_code']],
                $row
            );
        }
    }
}
