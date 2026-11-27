<?php

namespace App\Http\Controllers;

use App\Actions\Pendanaan\AttachDisbursementTaxInvoiceAction;
use App\Actions\Pendanaan\CancelDisbursementAction;
use App\Actions\Pendanaan\CreateDisbursementAction;
use App\Actions\Pendanaan\ListDisbursementsAction;
use App\Http\Requests\Pendanaan\StoreDisbursementRequest;
use App\Http\Requests\Pendanaan\UpdateDisbursementTaxInvoiceRequest;
use App\Models\Disbursement;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller Disbursement (Domain 4 — Entitas 13 PRD / PBI-12).
 * Mengatur lalu lintas HTTP pencairan anggaran termin ke vendor (CRUD).
 * Mematuhi prinsip Strict Thin Controller (AGENT_RULE.md).
 */
class DisbursementController extends Controller
{
    /**
     * Tampilkan halaman Manajemen Pencairan Anggaran & Log Termin Vendor dengan data DB.
     */
    public function index(ListDisbursementsAction $action): View
    {
        return view('pendanaan.pencairan', $action->execute());
    }

    /**
     * Catat mandat pencairan termin atau simpan draf baru (Create).
     */
    public function store(StoreDisbursementRequest $request, CreateDisbursementAction $action): RedirectResponse
    {
        $disbursement = $action->execute(
            $request->validated(),
            $request->file('receipt_file'),
            $request->file('tax_invoice_file')
        );

        $message = $disbursement->status === 'draft'
            ? "Draf {$disbursement->transaction_code} ({$disbursement->stage_label}) berhasil disimpan."
            : "Pencairan {$disbursement->stage_label} senilai {$disbursement->formatted_amount} ({$disbursement->transaction_code}) berhasil diotorisasi.";

        return redirect()->route('pendanaan.pencairan')->with('success', $message);
    }

    /**
     * Lampirkan Faktur Pajak e-Faktur susulan pada transaksi pencairan (Update).
     */
    public function attachTaxInvoice(
        UpdateDisbursementTaxInvoiceRequest $request,
        Disbursement $disbursement,
        AttachDisbursementTaxInvoiceAction $action
    ): RedirectResponse {
        $updated = $action->execute($disbursement, $request->file('tax_invoice_file'));

        return redirect()->route('pendanaan.pencairan')->with(
            'success',
            "Faktur Pajak susulan untuk transaksi {$updated->transaction_code} berhasil dilampirkan."
        );
    }

    /**
     * Anulir mutasi pencairan sebelum kliring bank selesai dan kembalikan budget_spent (Delete).
     */
    public function destroy(Disbursement $disbursement, CancelDisbursementAction $action): RedirectResponse
    {
        $cancelled = $action->execute($disbursement);

        return redirect()->route('pendanaan.pencairan')->with(
            'success',
            "Transaksi {$cancelled->transaction_code} berhasil dianulir dan pagu terpakai proyek telah disesuaikan."
        );
    }
}
