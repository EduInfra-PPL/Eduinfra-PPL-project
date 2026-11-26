<?php

namespace App\Http\Controllers;

use App\Actions\Pendanaan\ListDisbursementsAction;
use Illuminate\View\View;

/**
 * Controller Disbursement (Domain 4 — Entitas 13 PRD / PBI-12).
 * Mengatur lalu lintas HTTP pencairan anggaran termin ke vendor.
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
}
