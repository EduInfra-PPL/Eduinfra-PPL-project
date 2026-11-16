<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairProject extends Model
{
    use HasFactory;

    protected $table = 'repair_projects';

    protected $fillable = [
        'project_code',
        'school_id',
        'report_id',
        'damage_report_id',
        'vendor_id',
        'title',
        'description',
        'status',
        'progress_pct',
        'target_pct',
        'budget_allocated',
        'budget_spent',
        'contractor_name',
        'consortium_name',
        'surveyor_name',
        'inspector_notes',
        'priority',
        'target_start',
        'target_end',
        'start_date',
        'target_completion_date',
        'actual_completion_date',
    ];

    protected $casts = [
        'progress_pct' => 'float',
        'target_pct' => 'float',
        'budget_allocated' => 'float',
        'budget_spent' => 'float',
        'target_start' => 'date',
        'target_end' => 'date',
        'actual_completion_date' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class, 'report_id');
    }

    public function costEstimates(): HasMany
    {
        return $this->hasMany(CostEstimate::class, 'project_id')
            ->orderBy('item_code')
            ->orderBy('id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id')
            ->orderBy('milestone_code')
            ->orderBy('id');
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

        return $query->where(function (Builder $q) use ($keyword, $operator) {
            $q->where('project_code', $operator, "%{$keyword}%")
                ->orWhere('title', $operator, "%{$keyword}%")
                ->orWhere('contractor_name', $operator, "%{$keyword}%")
                ->orWhere('consortium_name', $operator, "%{$keyword}%")
                ->orWhereHas('school', function (Builder $sq) use ($keyword, $operator) {
                    $sq->where('name', $operator, "%{$keyword}%")
                        ->orWhere('npsn', 'like', "%{$keyword}%")
                        ->orWhereHas('region', function (Builder $rq) use ($keyword, $operator) {
                            $rq->where('name', $operator, "%{$keyword}%");
                        });
                });
        });
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (! $status || $status === 'all') {
            return $query;
        }

        return match ($status) {
            'termin1' => $query->where('progress_pct', '<', 30)->where('status', '!=', 'completed'),
            'termin2' => $query->whereBetween('progress_pct', [30, 89.99])->where('status', '!=', 'completed'),
            'termin3' => $query->where(function (Builder $q) {
                $q->where('progress_pct', '>=', 90)->orWhere('status', 'completed');
            }),
            'escrow_ready' => $query->whereColumn('progress_pct', '>=', 'target_pct')->where('status', '!=', 'completed'),
            'delayed' => $query->where(function (Builder $q) {
                $q->whereColumn('progress_pct', '<', 'target_pct')
                    ->orWhereHas('milestones', fn (Builder $mq) => $mq->where('status', 'delayed'));
            }),
            default => $query->where('status', $status),
        };
    }

    public function scopeFilterRegion(Builder $query, ?string $regionFilter): Builder
    {
        if (! $regionFilter || $regionFilter === 'all') {
            return $query;
        }

        if (is_numeric($regionFilter)) {
            return $query->whereHas('school', fn (Builder $sq) => $sq->where('region_id', (int) $regionFilter));
        }

        return match ($regionFilter) {
            'tier1' => $query->whereHas('school.region', fn (Builder $rq) => $rq->where('is_3t_tier', 1)),
            'tier2' => $query->whereHas('school.region', fn (Builder $rq) => $rq->where('is_3t_tier', 2)),
            '3t' => $query->whereHas('school.region', fn (Builder $rq) => $rq->where('is_3t_tier', '>', 0)),
            'non3t' => $query->whereHas('school.region', fn (Builder $rq) => $rq->where('is_3t_tier', 0)),
            default => $query->whereHas('school.region', fn (Builder $rq) => $rq->where('code', 'like', "{$regionFilter}%")),
        };
    }

    public function scopeFilterCurve(Builder $query, ?string $curve): Builder
    {
        if (! $curve || $curve === 'all') {
            return $query;
        }

        return match ($curve) {
            'ahead' => $query->whereRaw('progress_pct > target_pct'),
            'ontrack' => $query->whereRaw('progress_pct >= (target_pct - 5) AND progress_pct <= target_pct'),
            'delay' => $query->whereRaw('progress_pct < (target_pct - 5)'),
            default => $query,
        };
    }

    public function getDamageReportIdAttribute(): ?int
    {
        return $this->attributes['report_id'] ?? null;
    }

    public function setDamageReportIdAttribute(mixed $value): void
    {
        $this->attributes['report_id'] = $value;
    }

    public function getStartDateAttribute(): mixed
    {
        return $this->target_start;
    }

    public function setStartDateAttribute(mixed $value): void
    {
        $this->attributes['target_start'] = $value;
    }

    public function getTargetCompletionDateAttribute(): mixed
    {
        return $this->target_end;
    }

    public function setTargetCompletionDateAttribute(mixed $value): void
    {
        $this->attributes['target_end'] = $value;
    }

    public function getCodeAttribute(): string
    {
        return (string) $this->project_code;
    }

    public function getSchoolNameAttribute(): string
    {
        return $this->school?->name ?? 'Sekolah Belum Terhubung';
    }

    public function getNpsnAttribute(): string
    {
        return $this->school?->npsn ?: ($this->school?->osm_id ?: '-');
    }

    public function getLocationAttribute(): string
    {
        $regionName = $this->school?->region?->name;
        $address = $this->school?->address;

        if ($address && $regionName && ! str_contains(strtolower($address), strtolower($regionName))) {
            return "{$address}, {$regionName}";
        }

        return $regionName ?: ($address ?: 'Wilayah Pendidikan');
    }

    public function getZonasiAttribute(): string
    {
        $region = $this->school?->region;
        if (! $region) {
            return 'Wilayah Pendidikan';
        }

        if (class_exists(\App\Enums\ThreeTCategory::class)) {
            $label = \App\Enums\ThreeTCategory::labelFor($region);
            if ($label) {
                return '3T ' . $label;
            }
        }

        $tier = (int) ($region->is_3t_tier ?? 0);
        if ($tier === 1) {
            return 'Prioritas 3T Tier-1';
        }
        if ($tier === 2) {
            return 'Prioritas 3T Tier-2';
        }

        return 'Reguler Non-3T';
    }

    public function getCategoryLabelAttribute(): string
    {
        $cat = $this->damageReport?->category;
        if ($cat) {
            return $cat->sub_category ? "{$cat->name} ({$cat->sub_category})" : $cat->name;
        }

        $firstRabCat = $this->relationLoaded('costEstimates')
            ? $this->costEstimates->first()?->facilityCategory
            : null;

        if ($firstRabCat) {
            return $firstRabCat->sub_category
                ? "{$firstRabCat->name} ({$firstRabCat->sub_category})"
                : $firstRabCat->name;
        }

        return 'Rehabilitasi Sarana Sekolah';
    }

    public function getEffectiveBudgetAttribute(): float
    {
        if ($this->relationLoaded('costEstimates') && $this->costEstimates->isNotEmpty()) {
            $approvedSum = (float) $this->costEstimates
                ->whereIn('approval_status', ['Approved', 'approved'])
                ->sum('total_price');

            if ($approvedSum > 0) {
                return $approvedSum;
            }
        }

        return (float) $this->budget_allocated;
    }

    public function getContractAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_budget, 0, ',', '.');
    }

    public function getContractValueAttribute(): string
    {
        return $this->contract_amount;
    }

    public function getEscrowLockedAmountAttribute(): float
    {
        return max(0.0, $this->effective_budget - (float) $this->budget_spent);
    }

    public function getEscrowLockedAttribute(): string
    {
        return 'Rp ' . number_format($this->escrow_locked_amount, 0, ',', '.');
    }

    public function getActiveMilestoneAttribute(): ?ProjectMilestone
    {
        if (! $this->relationLoaded('milestones') || $this->milestones->isEmpty()) {
            return null;
        }

        return $this->milestones->firstWhere('status', 'in_progress')
            ?? $this->milestones->firstWhere('status', 'delayed')
            ?? $this->milestones->firstWhere('status', 'pending')
            ?? $this->milestones->last();
    }

    public function getCurrentStageAttribute(): string
    {
        if ($this->status === 'completed' || $this->progress_pct >= 100) {
            return 'Termin Final & BAST (100%)';
        }

        $active = $this->active_milestone;
        if ($active) {
            $code = $active->milestone_code ?: 'Tahap Aktif';
            return "{$code}: " . \Illuminate\Support\Str::limit($active->title, 28) . ' (' . round($this->progress_pct, 1) . '%)';
        }

        if ($this->progress_pct < 30) {
            return 'Termin 1 (Fisik ' . round($this->progress_pct, 1) . '%)';
        }
        if ($this->progress_pct < 90) {
            return 'Termin 2 (Fisik ' . round($this->progress_pct, 1) . '%)';
        }

        return 'Termin 3 (Fisik ' . round($this->progress_pct, 1) . '%)';
    }

    public function getStageBadgeAttribute(): string
    {
        $delta = $this->progress_pct - $this->target_pct;
        $scheduleNote = $delta > 0.5 ? 'Ahead of Schedule' : ($delta >= -5 ? 'On Track' : 'Perlu Akselerasi');

        if ($this->status === 'completed' || $this->progress_pct >= 100) {
            return 'Tuntas 100% • Siap BAST';
        }

        $terminNum = $this->progress_pct < 30 ? 1 : ($this->progress_pct < 90 ? 2 : 3);

        return "Termin {$terminNum} (Fisik " . round($this->progress_pct, 1) . "%) • {$scheduleNote}";
    }

    public function getEscrowReadyAttribute(): bool
    {
        if ($this->status === 'completed' || $this->progress_pct >= 100) {
            return false;
        }

        return $this->progress_pct >= $this->target_pct && $this->escrow_locked_amount > 0;
    }

    public function getEscrowTerminAttribute(): string
    {
        if ($this->status === 'completed' || $this->progress_pct >= 100) {
            return 'Seluruh Termin Tuntas';
        }

        if ($this->escrow_ready) {
            return 'Siap Otorisasi Pencairan';
        }

        return 'Escrow Terkunci (Progres < Target)';
    }

    public function getVendorNameAttribute(): string
    {
        return $this->contractor_name ?: 'Kontraktor Rekanan LPJK';
    }

    public function getScopeAttribute(): string
    {
        return $this->description ?: $this->title;
    }

    public function getPhotoThumbAttribute(): string
    {
        $activeWithProof = $this->relationLoaded('milestones')
            ? $this->milestones->whereNotNull('proof_media_url')->last()
            : null;

        if ($activeWithProof && $activeWithProof->proof_media_url) {
            return $activeWithProof->proof_media_url;
        }

        return 'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?auto=format&fit=crop&w=600&q=80';
    }

    public function getBadgePhoto1Attribute(): string
    {
        return 'Realisasi Fisik ' . round($this->progress_pct, 1) . '%';
    }

    public function getBadgePhoto2Attribute(): string
    {
        return $this->category_label;
    }

    public function getExifStatusAttribute(): string
    {
        $npsn = $this->npsn;
        return $npsn !== '-' ? "Geofence NPSN {$npsn}" : 'Geofence Titik Sekolah';
    }

    public function getLpjkSurveyorAttribute(): string
    {
        return $this->surveyor_name ?: 'Pengawas Sarpras Dinas';
    }

    public function getConsortiumAttribute(): string
    {
        return $this->consortium_name ?: 'Pendanaan APBD & Mitra CSR';
    }

    public function getAheadPctAttribute(): string
    {
        $diff = round($this->progress_pct - $this->target_pct, 1);
        return ($diff >= 0 ? '+' : '') . $diff . '%';
    }

    public function getFundedPctAttribute(): string
    {
        $spentPct = $this->effective_budget > 0
            ? round(($this->budget_spent / $this->effective_budget) * 100, 1)
            : 0;

        return "Realisasi Dana {$spentPct}%";
    }

    public function getSponsorshipStatusAttribute(): string
    {
        $ticket = $this->damageReport?->ticket_num;
        $prefix = $ticket ? "Tiket {$ticket} • " : '';
        return $prefix . 'Konstruksi Berjalan (' . round($this->progress_pct, 1) . '% Fisik)';
    }

    public function getEscrowAccountAttribute(): string
    {
        $regionCode = str_replace('.', '', $this->school?->region?->code ?? '3T');
        return "137-00-{$regionCode}-" . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT) . ' (Rekening Bersama Escrow)';
    }

    public function getTerminReleaseAmountAttribute(): float
    {
        $active = $this->active_milestone;
        if ($active && $active->weight_pct > 0) {
            return round($this->effective_budget * ($active->weight_pct / 100), 0);
        }

        return round($this->effective_budget * 0.30, 0);
    }

    public function getTermin2AmountAttribute(): string
    {
        return 'Rp ' . number_format($this->termin_release_amount, 0, ',', '.');
    }

    public function getInspectorNotesAttribute(): string
    {
        if (! empty($this->attributes['inspector_notes'])) {
            return (string) $this->attributes['inspector_notes'];
        }

        if ($this->relationLoaded('milestones')) {
            $latestMilestoneWithNote = $this->milestones->whereNotNull('notes')->filter(fn ($m) => trim((string) $m->notes) !== '')->last();
            if ($latestMilestoneWithNote) {
                return (string) $latestMilestoneWithNote->notes;
            }
        }

        if ($this->damageReport?->description) {
            return (string) $this->damageReport->description;
        }

        return 'Menunggu pembaruan catatan verifikasi fisik lapangan dari pengawas teknis.';
    }

    public function getStagesAttribute(): array
    {
        if (! $this->relationLoaded('milestones') || $this->milestones->isEmpty()) {
            return [];
        }

        $sorted = $this->milestones->sortBy(fn (ProjectMilestone $m) => $m->milestone_code ?? ('MS-' . str_pad((string) $m->id, 2, '0', STR_PAD_LEFT)))->values();

        return $sorted->map(function (ProjectMilestone $m, int $idx) {
            $dueDateStr = $m->due_date ? Carbon::parse($m->due_date)->translatedFormat('d M Y') : '-';
            $completedStr = $m->completed_at ? Carbon::parse($m->completed_at)->translatedFormat('d M Y') : null;
            $stageAmount = round($this->effective_budget * ($m->weight_pct / 100), 0);

            $verificationText = $m->status === 'completed' && $completedStr
                ? "Selesai {$completedStr} • Bobot {$m->weight_pct}% (" . ($m->surveyor_name ?: 'Terverifikasi') . ')'
                : 'Realisasi: ' . round($m->progress_pct, 1) . "% dari bobot {$m->weight_pct}% (Target: {$dueDateStr})";

            return [
                'id' => $m->id,
                'num' => $idx + 1,
                'milestone_code' => $m->milestone_code ?: ('MS-0' . ($idx + 1)),
                'name' => $m->title,
                'status' => $m->status_label,
                'raw_status' => $m->status,
                'amount' => 'Rp ' . number_format($stageAmount, 0, ',', '.'),
                'scope' => $m->description ?: $m->title,
                'notes' => $m->notes,
                'surveyor_name' => $m->surveyor_name,
                'verification' => $verificationText,
                'weight_pct' => $m->weight_pct,
                'progress_pct' => $m->progress_pct,
                'due_date' => $m->due_date?->format('Y-m-d'),
                'is_completed' => $m->status === 'completed',
                'is_active' => $m->status === 'in_progress',
                'is_delayed' => $m->status === 'delayed',
                'is_approved' => (bool) $m->is_approved,
            ];
        })->all();
    }

    public function getChecklistEscrowAttribute(): array
    {
        $completedMilestones = $this->relationLoaded('milestones')
            ? $this->milestones->where('status', 'completed')->count()
            : 0;
        $totalMilestones = $this->relationLoaded('milestones')
            ? max(1, $this->milestones->count())
            : 4;

        $approvedRabCount = $this->relationLoaded('costEstimates')
            ? $this->costEstimates->whereIn('approval_status', ['Approved', 'approved'])->count()
            : 0;
        $completedRabCount = $this->relationLoaded('costEstimates')
            ? $this->costEstimates->where('is_completed', true)->count()
            : 0;

        return [
            [
                'title' => 'Capaian Progres Fisik Terhadap Target Kurva S (' . round($this->target_pct, 1) . '%)',
                'desc' => 'Realisasi fisik saat ini ' . round($this->progress_pct, 1) . '% (' . $completedMilestones . ' dari ' . $totalMilestones . ' milestone tuntas)',
                'passed' => $this->progress_pct >= $this->target_pct,
            ],
            [
                'title' => 'Kesesuaian Komponen RAB & Taksonomi Sarpras',
                'desc' => $approvedRabCount > 0
                    ? "{$approvedRabCount} baris RAB terverifikasi ({$completedRabCount} item pekerjaan selesai di lapangan)"
                    : 'Menunggu penginputan rincian baris RAB proyek',
                'passed' => $approvedRabCount > 0,
            ],
            [
                'title' => 'Keterkaitan Master Sekolah & Wilayah (' . $this->zonasi . ')',
                'desc' => 'NPSN: ' . $this->npsn . ' • ' . $this->location,
                'passed' => $this->school_id !== null,
            ],
            [
                'title' => 'Pengesahan Pengawas Lapangan',
                'desc' => 'Verifikator: ' . $this->lpjk_surveyor,
                'passed' => ! empty($this->surveyor_name),
            ],
        ];
    }

    public function getPhotosComparisonAttribute(): array
    {
        $ticketLabel = $this->damageReport?->ticket_num
            ? 'Kondisi Awal (' . $this->damageReport->ticket_num . ')'
            : 'Kondisi Awal Pra-Renovasi';

        $activeMilestone = $this->active_milestone;
        $duringLabel = $activeMilestone
            ? 'Progres ' . ($activeMilestone->milestone_code ?: 'Tahap Aktif') . ' (' . round($this->progress_pct, 1) . '%)'
            : 'Pelaksanaan Fisik (' . round($this->progress_pct, 1) . '%)';

        return [
            'before' => [
                'label' => $ticketLabel,
                'geo' => 'NPSN: ' . $this->npsn . ' • ' . ($this->school?->region?->name ?? 'Lokasi Sekolah'),
                'note' => $this->damageReport?->description ?: ('Kondisi awal ' . strtolower($this->category_label) . ' sebelum intervensi renovasi.'),
                'url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=600&q=80',
            ],
            'during' => [
                'label' => $duringLabel,
                'geo' => 'Verifikator: ' . $this->lpjk_surveyor . ' • ' . $this->zonasi,
                'note' => $activeMilestone?->notes ?: ($activeMilestone?->description ?: $this->scope),
                'url' => $this->photo_thumb,
            ],
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'project_code';
    }
}
