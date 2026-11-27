<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Disbursement - Tabel disbursements (Domain 4: Entitas 13 PRD - PBI-12).
 * Mencatat mutasi pencairan dana escrow CSR secara bertahap ke vendor pelaksana.
 *
 * @property int $id
 * @property string $transaction_code
 * @property int $project_id
 * @property int $stage_num
 * @property int $amount
 * @property string|null $receipt_file
 * @property string|null $tax_invoice_file
 * @property \Illuminate\Support\Carbon|null $disbursed_at
 * @property int|null $authorized_by
 * @property string|null $vendor_name
 * @property string|null $vendor_bank_account
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Disbursement extends Model
{
    use HasFactory;

    protected $table = 'disbursements';

    protected $fillable = [
        'transaction_code',
        'project_id',
        'stage_num',
        'amount',
        'receipt_file',
        'tax_invoice_file',
        'disbursed_at',
        'authorized_by',
        'vendor_name',
        'vendor_bank_account',
        'status',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'stage_num' => 'integer',
        'amount' => 'integer',
        'authorized_by' => 'integer',
        'disbursed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI ELOQUENT
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke proyek renovasi sasaran (Domain 3).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(RepairProject::class, 'project_id');
    }

    /**
     * Relasi ke pejabat PPK yang mengotorisasi pencairan (Domain 5).
     */
    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    /*
    |--------------------------------------------------------------------------
    | LOCAL SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeDisbursed(Builder $query): Builder
    {
        return $query->where('status', 'disbursed');
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeNeedsTaxInvoice(Builder $query): Builder
    {
        return $query->whereNull('tax_invoice_file')->where('status', '!=', 'cancelled');
    }

    public function scopeByStage(Builder $query, int $stageNum): Builder
    {
        return $query->where('stage_num', $stageNum);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStageLabelAttribute(): string
    {
        return match ($this->stage_num) {
            1 => 'Termin 1 (30%)',
            2 => 'Termin 2 (40%)',
            3 => 'Termin 3 (Pelunasan 30%)',
            default => "Termin {$this->stage_num}",
        };
    }

    /**
     * Hitung total pagu RAB proyek dari item RAB Approved (fallback ke budget_allocated).
     */
    public static function resolveProjectRabTotal(RepairProject $project): int
    {
        $rabTotal = (int) CostEstimate::query()
            ->where('project_id', $project->id)
            ->whereIn('approval_status', ['Approved', 'approved'])
            ->sum('total_price');

        if ($rabTotal <= 0) {
            $rabTotal = (int) ($project->budget_allocated ?: 300_000_000);
        }

        return $rabTotal;
    }

    /**
     * Hitung pembagian pagu termin 30% / 40% / 30% berdasarkan total RAB proyek.
     *
     * @return array<int, int>
     */
    public static function calculateStageAmounts(int $rabTotal): array
    {
        $t1 = (int) round($rabTotal * 0.30);
        $t2 = (int) round($rabTotal * 0.40);
        $t3 = max(0, $rabTotal - $t1 - $t2);

        return [
            1 => $t1,
            2 => $t2,
            3 => $t3,
        ];
    }
}
