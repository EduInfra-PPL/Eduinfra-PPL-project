<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'company_name',
        'entity_type',
        'license_num',
        'nib',
        'contact_phone',
        'service_region_id',
        'grade',
        'qualification_label',
        'max_contract_limit',
        'sbu_status',
        'sbu_valid_until',
        'specialty',
        'logistics_note',
        'lead_engineer',
        'engineer_role',
        'rating',
        'review_count',
        'on_time_pct',
        'is_active',
    ];

    protected $casts = [
        'max_contract_limit' => 'float',
        'sbu_valid_until' => 'date',
        'rating' => 'float',
        'review_count' => 'integer',
        'on_time_pct' => 'float',
        'is_active' => 'boolean',
    ];

    public function serviceRegion(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'service_region_id');
    }

    public function repairProjects(): HasMany
    {
        return $this->hasMany(RepairProject::class, 'vendor_id')->latest('id');
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

        return $query->where(function (Builder $q) use ($keyword, $operator) {
            $q->where('company_name', $operator, "%{$keyword}%")
                ->orWhere('license_num', $operator, "%{$keyword}%")
                ->orWhere('nib', 'like', "%{$keyword}%")
                ->orWhere('lead_engineer', $operator, "%{$keyword}%")
                ->orWhere('specialty', $operator, "%{$keyword}%")
                ->orWhereHas('serviceRegion', function (Builder $rq) use ($keyword, $operator) {
                    $rq->where('name', $operator, "%{$keyword}%");
                });
        });
    }

    public function scopeFilterGrade(Builder $query, ?string $grade): Builder
    {
        if (! $grade || $grade === 'all') {
            return $query;
        }

        return match ($grade) {
            'small' => $query->whereIn('grade', ['K1', 'K2', 'K3']),
            'medium' => $query->whereIn('grade', ['M1', 'M2']),
            'large' => $query->whereIn('grade', ['B1', 'B2']),
            default => $query->where('grade', strtoupper($grade)),
        };
    }

    public function scopeFilterRegion(Builder $query, ?string $regionFilter): Builder
    {
        if (! $regionFilter || $regionFilter === 'all') {
            return $query;
        }

        if (is_numeric($regionFilter)) {
            return $query->where('service_region_id', (int) $regionFilter);
        }

        return match ($regionFilter) {
            '3t' => $query->whereHas('serviceRegion', fn (Builder $rq) => $rq->where('is_3t_tier', '>', 0)),
            'tier1' => $query->whereHas('serviceRegion', fn (Builder $rq) => $rq->where('is_3t_tier', 1)),
            'tier2' => $query->whereHas('serviceRegion', fn (Builder $rq) => $rq->where('is_3t_tier', 2)),
            'non3t' => $query->whereHas('serviceRegion', fn (Builder $rq) => $rq->where('is_3t_tier', 0)),
            default => $query,
        };
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (! $status || $status === 'all') {
            return $query;
        }

        return match ($status) {
            'active' => $query->where('is_active', true)->where('sbu_status', 'verified'),
            'sbu_review' => $query->where('is_active', true)->where('sbu_status', 'review'),
            'blacklist' => $query->where('is_active', false),
            default => $query,
        };
    }

    public function scopeFilterSpecialty(Builder $query, ?string $specialty): Builder
    {
        if (! $specialty || $specialty === 'all') {
            return $query;
        }

        $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

        return $query->where('specialty', $operator, "%{$specialty}%");
    }

    public function getInitialsAttribute(): string
    {
        $cleaned = preg_replace('/^(PT|CV|KSO|UD|BALAI)\s+/i', '', trim($this->company_name)) ?: $this->company_name;
        $words = preg_split('/\s+/', trim($cleaned)) ?: [];
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($cleaned, 0, 2));
    }

    public function getFormattedMaxLimitAttribute(): string
    {
        $limit = (float) $this->max_contract_limit;
        if ($limit >= 50_000_000_000) {
            return 'Pagu Maks: > Rp 50 Miliar';
        }
        if ($limit >= 1_000_000_000) {
            return 'Pagu Maks: Rp ' . number_format($limit / 1_000_000_000, 1, ',', '.') . ' Miliar / Paket';
        }
        if ($limit >= 1_000_000) {
            return 'Pagu Maks: Rp ' . number_format($limit / 1_000_000, 0, ',', '.') . ' Juta / Paket';
        }

        return 'Pagu Maks: Rp ' . number_format($limit, 0, ',', '.');
    }

    public function getRegionNameAttribute(): string
    {
        return $this->serviceRegion?->name ?? 'Lintas Wilayah Nasional';
    }

    public function getRegionClusterLabelAttribute(): string
    {
        $region = $this->serviceRegion;
        if (! $region) {
            return 'Cakupan Lintas Provinsi';
        }

        if (class_exists(\App\Enums\ThreeTCategory::class)) {
            $label = \App\Enums\ThreeTCategory::labelFor($region);
            if ($label) {
                return 'Klaster 3T ' . $label;
            }
        }

        $tier = (int) ($region->is_3t_tier ?? 0);
        if ($tier === 1) {
            return 'Klaster 3T Tier-1 (Perbatasan/Kepulauan)';
        }
        if ($tier === 2) {
            return 'Klaster 3T Tier-2 (Pedalaman/Terisolir)';
        }

        return 'Klaster Wilayah Reguler';
    }

    public function getSbuBadgeLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Izin Ditangguhkan (Blacklist)';
        }

        if ($this->sbu_status === 'review') {
            return 'SBU Masa Review / Perpanjangan';
        }

        return 'LPJK Terverifikasi';
    }

    public function getSbuValidityTextAttribute(): string
    {
        if (! $this->is_active) {
            return 'Dibekukan dari pengadaan proyek baru';
        }

        if ($this->sbu_status === 'review') {
            return 'Menunggu Validasi Perpanjangan SBU';
        }

        if ($this->sbu_valid_until) {
            return 'SBU Aktif s/d ' . $this->sbu_valid_until->format('Y');
        }

        return 'SBU Terakreditasi Aktif';
    }
}
