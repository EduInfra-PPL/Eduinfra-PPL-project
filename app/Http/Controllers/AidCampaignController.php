<?php

namespace App\Http\Controllers;

use App\Actions\Pendanaan\CreateAidCampaignAction;
use App\Actions\Pendanaan\ListAidCampaignsAction;
use App\Http\Requests\Pendanaan\StoreAidCampaignRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller AidCampaign (Domain 4: Pendanaan Bantuan & Matching CSR).
 * Mengatur lalu lintas HTTP katalog kampanye dan pembukaan program CSR baru.
 * Mematuhi prinsip Strict Thin Controller (AGENT_RULE.md).
 */
class AidCampaignController extends Controller
{
    /**
     * Tampilkan portal katalog Matching CSR beserta data proyek terverifikasi untuk modal.
     */
    public function index(Request $request, ListAidCampaignsAction $action): View
    {
        $keyword = $request->query('q');
        $filter = (string) $request->query('filter', 'all');

        $data = $action->execute($keyword, $filter);

        return view('pendanaan.index', array_merge($data, [
            'keyword' => $keyword,
            'activeFilter' => $filter,
        ]));
    }

    /**
     * Simpan kampanye bantuan CSR baru ke tabel `aid_campaigns`.
     */
    public function store(StoreAidCampaignRequest $request, CreateAidCampaignAction $action): RedirectResponse
    {
        $campaign = $action->execute($request->validated());

        return redirect()
            ->route('pendanaan.index')
            ->with('success', "Program CSR \"{$campaign->title}\" berhasil dipublikasikan dengan pagu " . $campaign->formatted_target . '.');
    }
}
