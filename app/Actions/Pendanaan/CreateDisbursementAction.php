<?php

namespace App\Actions\Pendanaan;

use App\Models\Disbursement;
use App\Models\RepairProject;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Action CreateDisbursementAction (Domain 4 — PBI-12 Create).
 * Mencatat pencairan dana termin (atau draf) dengan validasi termin berurutan,
 * penguncian pagu termin di sisi server (30/40/30%), unggah dokumen PDF,
 * dan pembaruan akumulasi `budget_spent` proyek dalam transaksi atomik.
 */
class CreateDisbursementAction
{
    /**
     * @param array<string, mixed> $validated
     */
    public function execute(
        array $validated,
        ?UploadedFile $receiptFile = null,
        ?UploadedFile $taxInvoiceFile = null
    ): Disbursement {
        return DB::transaction(function () use ($validated, $receiptFile, $taxInvoiceFile): Disbursement {
            /** @var RepairProject $project */
            $project = RepairProject::query()
                ->lockForUpdate()
                ->findOrFail((int) $validated['project_id']);

            $stageNum = (int) $validated['stage_num'];
            $status = ($validated['submit_mode'] ?? 'disbursed') === 'draft' ? 'draft' : 'disbursed';

            $existingActive = Disbursement::query()
                ->where('project_id', $project->id)
                ->where('stage_num', $stageNum)
                ->whereIn('status', ['disbursed', 'pending_approval'])
                ->exists();

            if ($existingActive) {
                throw ValidationException::withMessages([
                    'stage_num' => "Termin {$stageNum} untuk proyek ini sudah dicairkan atau sedang diproses.",
                ]);
            }

            if ($status === 'disbursed' && $stageNum > 1) {
                $previousStagePaid = Disbursement::query()
                    ->where('project_id', $project->id)
                    ->where('stage_num', $stageNum - 1)
                    ->where('status', 'disbursed')
                    ->exists();

                if (! $previousStagePaid) {
                    throw ValidationException::withMessages([
                        'stage_num' => "Termin " . ($stageNum - 1) . " harus dicairkan terlebih dahulu sebelum Termin {$stageNum}.",
                    ]);
                }
            }

            // Server-side amount lock dari RAB proyek (mengabaikan nominal manipulasi dari client)
            $rabTotal = Disbursement::resolveProjectRabTotal($project);
            $stageAmounts = Disbursement::calculateStageAmounts($rabTotal);
            $lockedAmount = $stageAmounts[$stageNum];

            $receiptPath = $receiptFile
                ? $receiptFile->store('disbursements/receipts', 'public')
                : null;
            $taxInvoicePath = $taxInvoiceFile
                ? $taxInvoiceFile->store('disbursements/tax-invoices', 'public')
                : null;

            $transactionCode = $this->generateUniqueTransactionCode();
            [$vendorName, $vendorBank] = $this->resolveVendorInfo($project);

            /** @var Disbursement $disbursement */
            $disbursement = Disbursement::query()->create([
                'transaction_code' => $transactionCode,
                'project_id' => $project->id,
                'stage_num' => $stageNum,
                'amount' => $lockedAmount,
                'receipt_file' => $receiptPath,
                'tax_invoice_file' => $taxInvoicePath,
                'disbursed_at' => $status === 'disbursed' ? now() : null,
                'authorized_by' => Auth::id(),
                'vendor_name' => $vendorName,
                'vendor_bank_account' => $vendorBank,
                'status' => $status,
            ]);

            if ($status === 'disbursed') {
                $project->update([
                    'budget_spent' => (float) $project->budget_spent + $lockedAmount,
                ]);
            }

            return $disbursement;
        });
    }

    private function generateUniqueTransactionCode(): string
    {
        $year = now()->format('Y');
        $nextNumber = ((int) Disbursement::query()->max('id')) + 101;
        $candidate = sprintf('TRX-DISB-%s-%03d', $year, $nextNumber);

        while (Disbursement::query()->where('transaction_code', $candidate)->exists()) {
            $nextNumber++;
            $candidate = sprintf('TRX-DISB-%s-%03d', $year, $nextNumber);
        }

        return $candidate;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function resolveVendorInfo(RepairProject $project): array
    {
        $vendorName = $project->contractor_name ?: 'Kontraktor Pelaksana';
        $vendorBank = 'Bank Mandiri •• 8821';

        if ($project->vendor_id && Schema::hasTable('vendors')) {
            $vendor = Vendor::query()->find($project->vendor_id);
            if ($vendor) {
                $vendorName = $vendor->company_name ?: $vendorName;
                if (! empty($vendor->bank_name) || ! empty($vendor->bank_account)) {
                    $accountTail = ! empty($vendor->bank_account) ? ' •• ' . substr((string) $vendor->bank_account, -4) : '';
                    $vendorBank = trim(($vendor->bank_name ?: 'Bank Giro') . $accountTail);
                }
            }
        }

        return [$vendorName, $vendorBank];
    }
}
