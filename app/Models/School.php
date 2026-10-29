<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabel schools (PBI-01). coordinates = titik master (PostGIS geography Point)
 * yang menjadi acuan geofence 250 m di Domain 2. Kolom geografi tidak diisi lewat
 * mass assignment; gunakan query spasial di controller.
 */
class School extends Model
{
    protected $table = 'schools';

    protected $fillable = ['accreditation', 'contact_phone', 'contact_email', 'is_active'];

    protected $hidden = ['coordinates'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Laporan kerusakan fisik pada sekolah ini (Domain 2 / PBI-04).
     */
    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'school_id');
    }
}
