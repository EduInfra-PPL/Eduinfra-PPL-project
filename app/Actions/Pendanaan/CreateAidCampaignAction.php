<?php

namespace App\Actions\Pendanaan;

use App\Models\AidCampaign;
use App\Services\Pendanaan\VerifiedProjectLookupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateAidCampaignAction
{
    public function __construct(
        protected VerifiedProjectLookupService $projectLookup
    ) {}

    /**
     * Eksekusi pembukaan program / kampanye CSR baru ke tabel `aid_campaigns`.
     * Target pagu pendanaan (`target_amount`) dikunci secara server-side
     * dari total RAB SNI proyek terkait agar tidak dapat dimanipulasi klien.
     *
     * @param array<string, mixed> $data
     */
    public function execute(array $data): AidCampaign
    {
        $project = $this->projectLookup->findById((int) $data['project_id']);

        if ($project === null || ($project['rab_amount'] ?? 0) <= 0) {
            throw ValidationException::withMessages([
                'project_id' => 'Proyek sasaran tidak ditemukan atau pagu RAB SNI belum tersedia.',
            ]);
        }

        return DB::transaction(function () use ($data, $project): AidCampaign {
            return AidCampaign::create([
                'project_id' => $project['id'],
                'title' => $data['title'],
                'target_amount' => (int) $project['rab_amount'],
                'collected_amount' => 0,
                'deadline' => $data['deadline'],
                'status' => 'open',
            ]);
        });
    }
}
