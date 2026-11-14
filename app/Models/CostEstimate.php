<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostEstimate extends Model
{
    use HasFactory;

    protected $table = 'cost_estimates';

    protected $fillable = [
        'project_id',
        'facility_category_id',
        'item_code',
        'category_group',
        'item_name',
        'specs',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
        'approval_status',
        'is_completed',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'total_price' => 'float',
        'is_completed' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(RepairProject::class, 'project_id');
    }

    public function facilityCategory(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'facility_category_id');
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
}
