<?php

namespace App\Http\Controllers\Renovasi;

use App\Actions\Renovasi\CreateRepairProjectAction;
use App\Actions\Renovasi\DeleteRepairProjectAction;
use App\Actions\Renovasi\ListRepairProjectsAction;
use App\Actions\Renovasi\UpdateProjectMilestoneAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Renovasi\StoreRepairProjectRequest;
use App\Http\Requests\Renovasi\UpdateProjectMilestoneRequest;
use App\Models\ProjectMilestone;
use App\Models\RepairProject;
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

    public function vendors(): RedirectResponse
    {
        return redirect()->route('renovasi.index');
    }
}
