<?php

namespace App\Models;

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

    /**
     * Laporan kerusakan yang tergolong dalam kategori fasilitas ini (Domain 2 / PBI-04).
     */
    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'category_id');
    }
}
