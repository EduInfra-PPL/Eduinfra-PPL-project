<?php

namespace App\Http\Controllers;

use App\Actions\Pendanaan\CreateAidContributionAction;
use App\Actions\Pendanaan\GetWallOfDonorsDataAction;
use App\Http\Requests\Pendanaan\StoreAidContributionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller AidContribution (Domain 4 — PBI-11).
 * Mengatur lalu lintas HTTP pencatatan komitmen CSR dan halaman Wall of Donors.
 * Mematuhi prinsip Strict Thin Controller (AGENT_RULE.md).
 */
class AidContributionController extends Controller
{
    /**
     * Tampilkan halaman Wall of Donors & Penyaluran CSR dengan data dinamis DB.
     */
    public function wallOfDonors(GetWallOfDonorsDataAction $action): View
    {
        return view('pendanaan.wall-of-donors', $action->execute());
    }

    /**
     * Simpan komitmen kontribusi CSR baru ke `aid_contributions` & update pagu kampanye.
     */
    public function store(StoreAidContributionRequest $request, CreateAidContributionAction $action): RedirectResponse
    {
        $contribution = $action->execute(
            $request->validated(),
            $request->file('payment_proof')
        );

        return redirect()
            ->route('pendanaan.index')
            ->with('success', "Komitmen CSR {$contribution->donor_name} sebesar {$contribution->formatted_amount} ({$contribution->transaction_code}) berhasil dicatat!");
    }
}
