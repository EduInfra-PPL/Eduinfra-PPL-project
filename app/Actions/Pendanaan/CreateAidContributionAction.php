<?php

namespace App\Actions\Pendanaan;

use App\Models\AidCampaign;
use App\Models\AidContribution;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Action CreateAidContributionAction (Domain 4 — PBI-11).
 * Mencatat komitmen bantuan CSR donatur, mengunggah bukti dokumen jika ada,
 * serta otomatis memperbarui akumulasi dana terkumpul dan status kampanye.
 */
class CreateAidContributionAction
{
    /**
     * @param array<string, mixed> $validated
     */
    public function execute(array $validated, ?UploadedFile $proofFile = null): AidContribution
    {
        return DB::transaction(function () use ($validated, $proofFile): AidContribution {
            /** @var AidCampaign $campaign */
            $campaign = AidCampaign::query()
                ->lockForUpdate()
                ->findOrFail((int) $validated['campaign_id']);

            $amount = (int) $validated['amount_or_goods'];

            $nextNumber = ((int) AidContribution::query()->max('id')) + 42;
            $transactionCode = 'TRX-CSR-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
            while (AidContribution::query()->where('transaction_code', $transactionCode)->exists()) {
                $nextNumber++;
                $transactionCode = 'TRX-CSR-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
            }

            $proofPath = $proofFile
                ? $proofFile->store('contributions/proofs', 'public')
                : null;

            $newCollected = $campaign->collected_amount + $amount;
            $progressRatio = $campaign->target_amount > 0 ? ($newCollected / $campaign->target_amount) : 0;

            $termStage = match (true) {
                $progressRatio >= 0.9 => 'Termin 3 Final',
                $progressRatio >= 0.5 => 'Termin 2 (Struktur)',
                default => 'Termin 1 (Pondasi)',
            };

            /** @var AidContribution $contribution */
            $contribution = AidContribution::query()->create([
                'transaction_code' => $transactionCode,
                'campaign_id' => $campaign->id,
                'donor_id' => Auth::id(),
                'donor_name' => (string) $validated['donor_name'],
                'donor_unit' => $validated['donor_unit'] ?? 'Divisi CSR & TJSL',
                'amount_or_goods' => $amount,
                'contribution_type' => (string) ($validated['contribution_type'] ?? 'cash'),
                'payment_proof' => $proofPath,
                'term_stage' => $termStage,
                'verification_note' => 'Terverifikasi BAP',
                'notes' => $validated['notes'] ?? null,
                'status' => 'verified',
            ]);

            $campaign->collected_amount = $newCollected;
            if ($campaign->collected_amount >= $campaign->target_amount) {
                $campaign->status = 'funded';
            }
            $campaign->save();

            return $contribution;
        });
    }
}
