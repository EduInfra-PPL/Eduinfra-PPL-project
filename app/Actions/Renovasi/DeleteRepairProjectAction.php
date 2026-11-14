<?php

namespace App\Actions\Renovasi;

use App\Models\DamageReport;
use App\Models\RepairProject;
use Illuminate\Support\Facades\DB;

class DeleteRepairProjectAction
{
    public function execute(RepairProject $project): string
    {
        $projectCode = $project->project_code;
        $reportId = $project->report_id;

        DB::transaction(function () use ($project, $reportId) {
            $project->milestones()->delete();
            $project->costEstimates()->delete();
            $project->delete();

            if ($reportId) {
                $otherActiveProject = RepairProject::where('report_id', $reportId)->exists();
                if (! $otherActiveProject) {
                    DamageReport::where('id', $reportId)->update(['status' => 'verified']);
                }
            }
        });

        return $projectCode;
    }
}
