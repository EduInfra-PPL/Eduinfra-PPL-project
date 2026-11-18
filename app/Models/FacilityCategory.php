<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tabel facility_categories (PBI-03): taksonomi fasilitas + bobot urgensi 1-5. */
class FacilityCategory extends Model
{
    protected $table = 'facility_categories';

    protected $fillable = ['name', 'sub_category', 'urgency_weight', 'default_unit', 'is_enabled'];

    protected $casts = [
        'urgency_weight' => 'integer',
        'is_enabled' => 'boolean',
    ];

    /** Batas bobot urgensi (FR-03). */
    public const MIN_WEIGHT = 1;

    public const MAX_WEIGHT = 5;

    /** Bobot urgensi dengan nilai ini ke atas dihitung "urgensi tinggi". */
    public const HIGH_WEIGHT = 4;

    /** Label tiap bobot urgensi untuk tampilan. */
    public const URGENCY_LABELS = [
        1 => 'Rendah',
        2 => 'Ringan',
        3 => 'Sedang',
        4 => 'Tinggi',
        5 => 'Kritis',
    ];

    public static function urgencyLabel(int $weight): string
    {
        return self::URGENCY_LABELS[$weight] ?? (string) $weight;
    }

    /** Hanya kategori aktif (yang boleh dipilih di formulir laporan). */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }

    /** Urutan baku taksonomi: kategori utama, lalu sub-kategori. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name')->orderBy('sub_category');
    }

    /** Nama lengkap: "Kategori - Sub-kategori" (sub-kategori boleh kosong). */
    public function getFullNameAttribute(): string
    {
        return $this->sub_category !== '' && $this->sub_category !== null
            ? "{$this->name} - {$this->sub_category}"
            : $this->name;
    }

    /**
     * Laporan kerusakan yang tergolong dalam kategori fasilitas ini (Domain 2 / PBI-04).
     */
    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'category_id');
    }
}
