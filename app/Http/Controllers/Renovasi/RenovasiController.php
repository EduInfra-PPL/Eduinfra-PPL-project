<?php

namespace App\Http\Controllers\Renovasi;

use App\Actions\Renovasi\CreateRepairProjectAction;
use App\Actions\Renovasi\CreateVendorAction;
use App\Actions\Renovasi\DeleteRepairProjectAction;
use App\Actions\Renovasi\DeleteVendorAction;
use App\Actions\Renovasi\ListRepairProjectsAction;
use App\Actions\Renovasi\ListVendorsAction;
use App\Actions\Renovasi\ToggleVendorBlacklistAction;
use App\Actions\Renovasi\UpdateProjectMilestoneAction;
use App\Actions\Renovasi\UpdateVendorAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Renovasi\StoreRepairProjectRequest;
use App\Http\Requests\Renovasi\StoreVendorRequest;
use App\Http\Requests\Renovasi\UpdateProjectMilestoneRequest;
use App\Http\Requests\Renovasi\UpdateVendorRequest;
use App\Models\ProjectMilestone;
use App\Models\RepairProject;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RenovasiController extends Controller
{
    public function index(Request $request, ListRepairProjectsAction $action): View
    {
        return view('renovasi.projects.index', $action->getIndexPayload($request->only([
            'view', 'search', 'status', 'region',
        ])));
    }

    public function store(StoreRepairProjectRequest $request, CreateRepairProjectAction $action): RedirectResponse
    {
        $project = $action->execute($request->validated());

        return redirect()->route('renovasi.projects.show', $project->project_code)
            ->with('success', "Proyek renovasi {$project->project_code} berhasil dibuka dan dihubungkan dengan Master Data.");
    }

    public function showProject(string $id): View
    {
        $query = RepairProject::with([
            'school.region',
            'damageReport.category',
            'vendor.serviceRegion',
            'milestones',
            'costEstimates.facilityCategory',
        ]);

        $project = is_numeric($id)
            ? $query->where('id', (int) $id)->firstOrFail()
            : $query->where('project_code', $id)->firstOrFail();

        return view('renovasi.projects.show', compact('project'));
    }

    public function updateMilestone(
        UpdateProjectMilestoneRequest $request,
        int $id,
        UpdateProjectMilestoneAction $action
    ): RedirectResponse {
        $milestone = ProjectMilestone::findOrFail($id);
        $updated = $action->execute($milestone, $request->validated(), $request->file('proof_photo'));

        return back()->with(
            'success',
            "Milestone [{$updated->milestone_code}] {$updated->title} diperbarui ke " . round($updated->progress_pct) . '%.'
        );
    }

    public function destroy(string $id, DeleteRepairProjectAction $action): RedirectResponse
    {
        $project = is_numeric($id)
            ? RepairProject::findOrFail((int) $id)
            : RepairProject::where('project_code', $id)->firstOrFail();

        $code = $action->execute($project);

        return redirect()->route('renovasi.index')
            ->with('success', "Proyek {$code} berhasil dibatalkan dan dihapus dari daftar pengerjaan.");
    }

    public function milestones(Request $request, ListRepairProjectsAction $action): View
    {
        return view('renovasi.projects.milestones-summary', $action->getMilestonesPayload($request->only([
            'search', 'termin', 'curve', 'region',
        ])));
    }

    public function rab(): RedirectResponse
    {
        return redirect()->route('renovasi.index', ['view' => 'table']);
    }

    public function showProjectRab(string $id): RedirectResponse
    {
        return redirect()->route('renovasi.projects.show', $id);
    }

    public function vendors(Request $request, ListVendorsAction $action): View
    {
        return view('renovasi.vendors.index', $action->execute($request->only([
            'search', 'grade', 'region', 'status', 'specialty',
        ])));
    }

    public function storeVendor(StoreVendorRequest $request, CreateVendorAction $action): RedirectResponse
    {
        $vendor = $action->execute($request->validated());

        return redirect()->route('renovasi.vendors')
            ->with('success', "Kontraktor rekanan {$vendor->company_name} ({$vendor->license_num}) berhasil diregistrasikan ke direktori LPJK.");
    }

    public function updateVendor(UpdateVendorRequest $request, Vendor $vendor, UpdateVendorAction $action): RedirectResponse
    {
        $updated = $action->execute($vendor, $request->validated());

        return redirect()->route('renovasi.vendors')
            ->with('success', "Data dan dokumen SBU rekanan {$updated->company_name} berhasil diperbarui.");
    }

    public function toggleBlacklistVendor(Vendor $vendor, ToggleVendorBlacklistAction $action): RedirectResponse
    {
        $updated = $action->execute($vendor);
        $statusLabel = $updated->is_active ? 'dipulihkan ke daftar rekanan aktif' : 'dimasukkan ke daftar blacklist (ditangguhkan)';

        return redirect()->route('renovasi.vendors')
            ->with('success', "Status izin LPJK {$updated->company_name} berhasil {$statusLabel}.");
    }

    public function destroyVendor(Vendor $vendor, DeleteVendorAction $action): RedirectResponse
    {
        $name = $action->execute($vendor);

        return redirect()->route('renovasi.vendors')
            ->with('success', "Data rekanan {$name} berhasil dihapus dari direktori.");
    }
}
