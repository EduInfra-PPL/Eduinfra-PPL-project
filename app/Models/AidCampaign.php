<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model AidCampaign - Tabel aid_campaigns (Domain 4: Entitas 11 PRD).
 * Menyimpan program / kampanye penggalangan dana CSR untuk proyek renovasi sekolah 3T.
 *
 * @property int $id
 * @property int $project_id
 * @property string $title
 * @property int $target_amount
 * @property int $collected_amount
 * @property \Illuminate\Support\Carbon $deadline
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class AidCampaign extends Model
{
    use HasFactory;

    protected $table = 'aid_campaigns';

    protected $fillable = [
        'project_id',
        'title',
        'target_amount',
        'collected_amount',
        'deadline',
        'status',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'target_amount' => 'integer',
        'collected_amount' => 'integer',
        'deadline' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI ELOQUENT
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke proyek renovasi Domain 3 (aktif otomatis setelah Model RepairProject tersedia).
     */
    public function repairProject(): BelongsTo
    {
        return $this->belongsTo('App\Models\RepairProject', 'project_id');
    }

    /*
    |--------------------------------------------------------------------------
    | LOCAL SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope kampanye yang sedang aktif menerima komitmen CSR.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope kampanye dengan kebutuhan pagu di bawah nominal tertentu (mis. Rp 50 Jt).
     */
    public function scopeUnderAmount(Builder $query, int $maxAmount): Builder
    {
        return $query->where('target_amount', '<', $maxAmount);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (PRESENTASI UI)
    |--------------------------------------------------------------------------
    */

    /**
     * Persentase ketercapaian dana CSR (0 - 100%).
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->collected_amount / $this->target_amount) * 100));
    }

    /**
     * Sisa kebutuhan dana hingga target pagu RAB terpenuhi.
     */
    public function getRemainingAmountAttribute(): int
    {
        return max(0, $this->target_amount - $this->collected_amount);
    }

    /**
     * Format ringkas sisa dana untuk kartu UI (contoh: "Rp 315 Juta" / "Rp 55,5 Juta").
     */
    public function getFormattedRemainingAttribute(): string
    {
        $remaining = $this->remaining_amount;

        if ($remaining >= 1_000_000) {
            $juta = $remaining / 1_000_000;
            $formatted = fmod($juta, 1) === 0.0
                ? number_format($juta, 0, ',', '.')
                : rtrim(rtrim(number_format($juta, 1, ',', '.'), '0'), ',');

            return "Rp {$formatted} Juta";
        }

        return 'Rp ' . number_format($remaining, 0, ',', '.');
    }

    /**
     * Format nominal target RAB SNI lengkap.
     */
    public function getFormattedTargetAttribute(): string
    {
        return 'Rp ' . number_format($this->target_amount, 0, ',', '.');
    }
}
