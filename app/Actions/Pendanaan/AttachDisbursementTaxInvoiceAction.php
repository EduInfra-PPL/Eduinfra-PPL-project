<?php

namespace App\Actions\Pendanaan;

use App\Models\Disbursement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Action AttachDisbursementTaxInvoiceAction (Domain 4 — PBI-12 Update).
 * Melampirkan berkas Faktur Pajak (e-Faktur PDF) susulan pada transaksi pencairan.
 */
class AttachDisbursementTaxInvoiceAction
{
    public function execute(Disbursement $disbursement, UploadedFile $taxInvoiceFile): Disbursement
    {
        return DB::transaction(function () use ($disbursement, $taxInvoiceFile): Disbursement {
            /** @var Disbursement $locked */
            $locked = Disbursement::query()
                ->lockForUpdate()
                ->findOrFail($disbursement->id);

            if ($locked->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'tax_invoice_file' => 'Tidak dapat melampirkan faktur pajak pada transaksi yang sudah dianulir.',
                ]);
            }

            $oldPath = $locked->tax_invoice_file;
            $newPath = $taxInvoiceFile->store('disbursements/tax-invoices', 'public');

            $locked->update([
                'tax_invoice_file' => $newPath,
            ]);

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            return $locked->fresh();
        });
    }
}
