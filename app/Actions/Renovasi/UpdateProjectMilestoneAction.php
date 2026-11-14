<?php

namespace App\Actions\Renovasi;

use App\Models\CostEstimate;
use App\Models\ProjectMilestone;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateProjectMilestoneAction
{
    public function execute(ProjectMilestone $milestone, array $data, ?UploadedFile $proofPhoto = null): ProjectMilestone
    {
        return DB::transaction(function () use ($milestone, $data, $proofPhoto) {
            $progressPct = (float) $data['progress_pct'];
            $status = $data['status'];

            if ($progressPct >= 100.0) {
                $progressPct = 100.00;
                $status = 'completed';
            } elseif ($progressPct <= 0.0 && $status === 'completed') {
                $status = 'pending';
            } elseif ($progressPct > 0.0 && $progressPct < 100.0 && $status === 'pending') {
                $status = 'in_progress';
            }

            $completedAt = $milestone->completed_at;
            if ($status === 'completed' && ! $completedAt) {
                $completedAt = ! empty($data['assessment_date'])
                    ? Carbon::parse($data['assessment_date'])
                    : now();
            } elseif ($status !== 'completed') {
                $completedAt = null;
            }

            $evidencePhotos = is_array($milestone->evidence_photos) ? $milestone->evidence_photos : [];
            $proofMediaUrl = $milestone->proof_media_url;

            if ($proofPhoto) {
                $storedPath = $proofPhoto->store('renovasi/milestones', 'public');
                $proofMediaUrl = asset('storage/' . $storedPath);
                $evidencePhotos[] = [
                    'name' => $proofPhoto->getClientOriginalName(),
                    'size' => round($proofPhoto->getSize() / 1024, 1) . ' KB',
                    'status' => 'Terunggah (' . now()->format('d M Y H:i') . ')',
                    'url' => $proofMediaUrl,
                ];
            }

            $milestone->update([
                'progress_pct' => $progressPct,
                'status' => $status,
                'surveyor_name' => $data['surveyor_name'] ?? $milestone->surveyor_name,
                'notes' => $data['notes'] ?? $milestone->notes,
                'completed_at' => $completedAt,
                'is_approved' => $status === 'completed',
                'proof_media_url' => $proofMediaUrl,
                'evidence_photos' => $evidencePhotos,
            ]);

            $project = $milestone->project()->with(['milestones', 'costEstimates', 'damageReport'])->first();

            if (! empty($data['sync_estimates'])) {
                $checkedIds = array_map('intval', $data['completed_estimates'] ?? []);
                CostEstimate::where('project_id', $project->id)
                    ->update(['is_completed' => false]);

                if ($checkedIds !== []) {
                    CostEstimate::where('project_id', $project->id)
                        ->whereIn('id', $checkedIds)
                        ->update(['is_completed' => true]);
                }
            }

            $allMilestones = $project->milestones()->orderBy('milestone_code')->orderBy('id')->get();
            $totalWeight = (float) $allMilestones->sum('weight_pct');

            $totalProgress = $totalWeight > 0
                ? $allMilestones->sum(fn (ProjectMilestone $m) => ($m->weight_pct * $m->progress_pct) / $totalWeight)
                : $allMilestones->avg('progress_pct');

            $totalProgress = round(min(100.0, max(0.0, (float) $totalProgress)), 2);

            // Jika sebuah milestone telah selesai, aktifkan milestone berikutnya yang masih berstatus pending.
            if ($status === 'completed') {
                $hasInProgress = $allMilestones->contains(fn (ProjectMilestone $m) => $m->status === 'in_progress');
                if (! $hasInProgress) {
                    $nextPending = $allMilestones->firstWhere('status', 'pending');
                    $nextPending?->update(['status' => 'in_progress']);
                }
            }

            $projectUpdate = [
                'progress_pct' => $totalProgress,
                'budget_spent' => round($project->effective_budget * ($totalProgress / 100), 0),
            ];

            if (! empty($data['surveyor_name'])) {
                $projectUpdate['surveyor_name'] = $data['surveyor_name'];
            }
            if (! empty($data['notes'])) {
                $projectUpdate['inspector_notes'] = $data['notes'];
            }

            if ($totalProgress >= 100.0) {
                $projectUpdate['status'] = 'completed';
                $projectUpdate['actual_completion_date'] = now()->toDateString();

                if ($project->damageReport) {
                    $project->damageReport->update(['status' => 'resolved']);
                }
            } else {
                $projectUpdate['status'] = 'in_progress';
                $projectUpdate['actual_completion_date'] = null;

                if ($project->damageReport && $project->damageReport->status === 'resolved') {
                    $project->damageReport->update(['status' => 'in_repair']);
                }
            }

            $project->update($projectUpdate);

            return $milestone->fresh();
        });
    }
}
