<?php

namespace App\Actions\Pendanaan;

use App\Models\Disbursement;
use App\Models\RepairProject;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Action CancelDisbursementAction (Domain 4 — PBI-12 Delete/Anulir).
 * Menganulir mutasi pencairan sebelum kliring bank selesai dengan menjaga jejak audit
 * (`status = 'cancelled'`) dan mengembalikan saldo `budget_spent` pada proyek induk.
 */
class CancelDisbursementAction
{
    public function execute(Disbursement $disbursement): Disbursement
    {
        return DB::transaction(function () use ($disbursement): Disbursement {
            /** @var Disbursement $locked */
            $locked = Disbursement::query()
                ->lockForUpdate()
                ->findOrFail($disbursement->id);

            if ($locked->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'disbursement' => 'Transaksi pencairan ini sudah berstatus dibatalkan/dianulir.',
                ]);
            }

            $hasLaterDisbursedStage = Disbursement::query()
                ->where('project_id', $locked->project_id)
                ->where('stage_num', '>', $locked->stage_num)
                ->where('status', 'disbursed')
                ->exists();

            if ($hasLaterDisbursedStage) {
                throw ValidationException::withMessages([
                    'disbursement' => "Tidak dapat menganulir Termin {$locked->stage_num} karena termin berikutnya sudah dicairkan.",
                ]);
            }

            if ($locked->status === 'disbursed') {
                /** @var RepairProject|null $project */
                $project = RepairProject::query()
                    ->lockForUpdate()
                    ->find($locked->project_id);

                if ($project) {
                    $project->update([
                        'budget_spent' => max(0, (float) $project->budget_spent - (float) $locked->amount),
                    ]);
                }
            }

            $locked->update([
                'status' => 'cancelled',
                'disbursed_at' => null,
            ]);

            return $locked->fresh();
        });
    }
}
