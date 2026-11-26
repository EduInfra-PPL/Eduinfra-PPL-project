<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model AuditLog (Domain 2: PBI-06).
 * Merepresentasikan lembar hasil inspeksi dan log verifikasi lapangan oleh auditor resmi.
 *
 * @property int $id
 * @property int $report_id
 * @property int|null $auditor_id
 * @property \Illuminate\Support\Carbon $inspection_date
 * @property string|float $physical_score
 * @property string $audit_status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class AuditLog extends Model
{
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_NEEDS_REVIEW = 'Needs_Review';
    public const STATUS_REJECTED = 'Rejected';

    public const STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_NEEDS_REVIEW,
        self::STATUS_REJECTED,
    ];

    protected $table = 'audit_logs';

    protected $fillable = [
        'report_id',
        'auditor_id',
        'inspection_date',
        'physical_score',
        'audit_status',
        'notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'physical_score' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI ELOQUENT
    |--------------------------------------------------------------------------
    */

    /**
     * Laporan kerusakan yang diaudit (PBI-04).
     */
    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class, 'report_id');
    }

    /**
     * Auditor / Verifikator resmi yang melakukan inspeksi (Domain 5).
     */
    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope log audit yang disetujui.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('audit_status', self::STATUS_APPROVED);
    }

    /**
     * Scope log audit yang membutuhkan tinjauan ulang.
     */
    public function scopeNeedsReview(Builder $query): Builder
    {
        return $query->where('audit_status', self::STATUS_NEEDS_REVIEW);
    }

    /**
     * Scope log audit yang ditolak.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('audit_status', self::STATUS_REJECTED);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Presentasi UI)
    |--------------------------------------------------------------------------
    */

    /**
     * Label status audit berbahasa Indonesia yang ramah pengguna.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->audit_status) {
            self::STATUS_APPROVED => 'Disetujui (Verified)',
            self::STATUS_NEEDS_REVIEW => 'Perlu Tinjauan Ulang',
            self::STATUS_REJECTED => 'Ditolak',
            default => (string) $this->audit_status,
        };
    }

    /**
     * Kelas badge Tailwind untuk status audit.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->audit_status) {
            self::STATUS_APPROVED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_NEEDS_REVIEW => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_REJECTED => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
