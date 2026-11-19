<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabel regions (PBI-02). boundary_polygon bertipe PostGIS geography dan besar,
 * jadi disembunyikan; ambil lewat query spasial (ST_AsGeoJSON) di RegionQueryService.
 */
class Region extends Model
{
    /** Tingkat wilayah administrasi. */
    public const LEVELS = ['prov', 'kab', 'kec'];

    /** is_3t_tier: 0 = bukan 3T, 1 = Tier-1 (prioritas lebih tinggi), 2 = Tier-2. */
    public const THREE_T_TIERS = [0, 1, 2];

    protected $table = 'regions';

    /** is_3t (kolom turunan) tidak ikut fillable: dihitung oleh database. */
    protected $fillable = ['code', 'name', 'level', 'is_3t_tier', 'is_tertinggal', 'is_terdepan', 'is_terluar'];

    protected $hidden = ['boundary_polygon'];

    protected $casts = [
        'is_3t_tier' => 'integer',
        'is_3t' => 'boolean',
        'is_tertinggal' => 'boolean',
        'is_terdepan' => 'boolean',
        'is_terluar' => 'boolean',
    ];

    /** Kolom non-geometri; pakai ->select(Region::COLUMNS) agar polygon tidak ikut terambil. */
    public const COLUMNS = [
        'id', 'code', 'name', 'level', 'is_3t_tier', 'is_3t',
        'is_tertinggal', 'is_terdepan', 'is_terluar', 'created_at', 'updated_at',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'region_id');
    }
}
