<?php

namespace Database\Seeders;

use App\Models\AidCampaign;
use Illuminate\Database\Seeder;

class AidCampaignSeeder extends Seeder
{
    /**
     * Mengisi data awal katalog kampanye CSR Domain 4 (`aid_campaigns`).
     * Proyek ID 1..3 diisi sebagai kampanye aktif, sementara proyek ID 4..6
     * dibiarkan kosong agar tampil pada pilihan modal "Buka Program / Kampanye CSR Baru".
     */
    public function run(): void
    {
        $campaigns = [
            [
                'project_id' => 1,
                'title' => 'Rekonstruksi 3 ruang kelas roboh akibat longsor dan penyediaan 1 unit PLTS komunal 3000 Wp.',
                'target_amount' => 420_000_000,
                'collected_amount' => 105_000_000,
                'deadline' => now()->addDays(45)->toDateString(),
                'status' => 'open',
            ],
            [
                'project_id' => 2,
                'title' => 'Renovasi atap baja ringan, perbaikan plafon lapuk, dan instalasi tangki pemanen air hujan bersih.',
                'target_amount' => 185_000_000,
                'collected_amount' => 129_500_000,
                'deadline' => now()->addDays(30)->toDateString(),
                'status' => 'open',
            ],
            [
                'project_id' => 3,
                'title' => 'Pengadaan ruang komputer digital, perangkat satelit Starlink, dan laboratorium sains dasar anak TKI.',
                'target_amount' => 95_000_000,
                'collected_amount' => 42_750_000,
                'deadline' => now()->addDays(60)->toDateString(),
                'status' => 'open',
            ],
        ];

        foreach ($campaigns as $campaign) {
            AidCampaign::updateOrCreate(
                ['project_id' => $campaign['project_id']],
                $campaign
            );
        }
    }
}
