<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabel regions (PBI-02). boundary_polygon bertipe PostGIS geography dan besar,
 * jadi disembunyikan; ambil lewat query spasial (ST_AsGeoJSON) di controller.
 */
class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = ['code', 'name', 'level', 'is_3t_tier'];

    protected $hidden = ['boundary_polygon'];

    protected $casts = ['is_3t_tier' => 'integer'];

    /** Kolom non-geometri; pakai ->select(Region::COLUMNS) agar polygon tidak ikut terambil. */
    public const COLUMNS = ['id', 'code', 'name', 'level', 'is_3t_tier', 'created_at', 'updated_at'];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'region_id');
    }
}
