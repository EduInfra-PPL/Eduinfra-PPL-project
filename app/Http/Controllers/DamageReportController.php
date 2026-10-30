<?php

namespace App\Http\Controllers;

use App\Actions\Reports\CreateDamageReportAction;
use App\Http\Requests\Reports\StoreDamageReportRequest;
use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller DamageReport (Domain 2: PBI-04).
 * Mengatur lalu lintas HTTP untuk direktori dan formulir pelaporan kerusakan fisik.
 * Mematuhi prinsip Strict Thin Controller (AGENT_RULE.md).
 */
class DamageReportController extends Controller
{
    /**
     * Tampilkan direktori daftar tiket laporan kerusakan dengan filter & KPI.
     */
    public function index(Request $request): View
    {
        $keyword = $request->query('q');
        $status = $request->query('status');
        $severity = $request->query('severity');
        $level = $request->query('level');

        $reportsQuery = DamageReport::with(['school.region', 'category', 'reporter'])
            ->latest('id');

        if ($keyword) {
            $reportsQuery->search($keyword);
        }

        if ($status && $status !== 'all') {
            $reportsQuery->where('status', $status);
        }

        if ($severity && $severity !== 'all') {
            $reportsQuery->where('severity_level', (int) $severity);
        }

        if ($level && $level !== 'all') {
            $reportsQuery->whereHas('school', fn ($q) => $q->where('educational_level', $level));
        }

        $reports = $reportsQuery->paginate(10)->withQueryString();

        $kpi = [
            'total' => DamageReport::count(),
            'pending' => DamageReport::pending()->count(),
            'verified' => DamageReport::verified()->count(),
            'urgent' => DamageReport::urgent()->count(),
        ];

        return view('reports.index', compact('reports', 'kpi', 'keyword', 'status', 'severity', 'level'));
    }

    /**
     * Tampilkan formulir pelaporan kerusakan baru (Langkah 1).
     */
    public function create(): View
    {
        $schools = School::active()->with('region')->orderBy('name')->get();
        $categories = FacilityCategory::where('is_enabled', true)->orderBy('urgency_weight', 'desc')->get();

        return view('reports.create', compact('schools', 'categories'));
    }

    /**
     * Simpan laporan kerusakan baru dari formulir publik.
     */
    public function store(StoreDamageReportRequest $request, CreateDamageReportAction $action): RedirectResponse
    {
        $report = $action->execute($request->validated());

        return redirect()->route('reports.index')
            ->with('success', "Laporan kerusakan berhasil disimpan dengan nomor tiket: {$report->ticket_num}");
    }
}
