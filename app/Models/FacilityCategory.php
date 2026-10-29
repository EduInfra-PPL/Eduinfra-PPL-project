<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Tabel facility_categories (PBI-03): taksonomi fasilitas + bobot urgensi 1-5. */
class FacilityCategory extends Model
{
    protected $table = 'facility_categories';

    protected $fillable = ['name', 'sub_category', 'urgency_weight', 'default_unit', 'is_enabled'];

    protected $casts = [
        'urgency_weight' => 'integer',
        'is_enabled' => 'boolean',
    ];
}
